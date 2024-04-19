<?php

declare(strict_types=1);

namespace Norvica\Validation;

use DateTimeInterface;
use Norvica\Validation\Exception\LogicException;
use Norvica\Validation\Exception\NormalizationException;
use Norvica\Validation\Exception\PropertyRuleViolation;
use Norvica\Validation\Instruction\AndX;
use Norvica\Validation\Instruction\EachX;
use Norvica\Validation\Instruction\OptionalX;
use Norvica\Validation\Instruction\OrX;
use Norvica\Validation\Registry\MapRegistry;
use Norvica\Validation\Registry\Registry;
use Norvica\Validation\Rule\Rule;
use Norvica\Validation\Exception\ValueRuleViolation;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Violation\Violation;
use ReflectionClass;
use ReflectionProperty;
use stdClass;
use UnexpectedValueException;

final class Validator
{
    private Registry $registry;
    private Options $options;

    public function __construct(
        Registry|array $registry = [],
        Options $options = null,
    ) {
        $default = Options::default();
        $this->registry = is_array($registry) ? new MapRegistry($registry) : $registry;
        $this->options = $options ? $default->merge($options) : $default;
    }

    /**
     * @throws PropertyRuleViolation
     */
    public function validate(
        mixed $value,
        Rule|OptionalX|EachX|AndX|OrX|array|null $rules = null,
        Options $options = null,
    ): Result {
        $violations = [];
        $normalized = $this->traverse(
            [],
            $options ? $this->options->merge($options) : $this->options,
            $value,
            $rules,
            $violations,
        );

        return new Result(violations: $violations, normalized: $normalized);
    }

    /**
     * @param string[] $path
     * @throws ValueRuleViolation
     */
    private function traverse(
        array $path,
        Options $options,
        mixed $value,
        Rule|OptionalX|EachX|AndX|OrX|array|null $rules,
        array &$violations,
    ): object|array|string|int|float|bool|null {
        if ($value === null) {
            if (!$rules instanceof OptionalX) {
                $this->violation($violations, $path, 'Value is required.', $options);
            }

            return null;
        }

        if ($rules instanceof OptionalX) {
            return $this->optionalX($path, $options, $value, $rules, $violations);
        }

        if ($rules instanceof AndX) {
            return $this->andX($path, $options, $value, $rules, $violations);
        }

        if ($rules instanceof OrX) {
            return $this->orX($path, $options, $value, $rules, $violations);
        }

        // scalar
        if (is_scalar($value)) {
            return $this->single($path, $options, $value, $rules, $violations);
        }

        // list
        if (is_array($value) && array_is_list($value)) {
            return match (true) {
                $rules instanceof EachX => $this->list($path, $options, $value, $rules, $violations),
                $rules instanceof Rule => $this->single($path, $options, $value, $rules, $violations),
                default => $this->array($path, $options, $value, $rules, $violations),
            };
        }

        // associative array
        if (is_array($value)) {
            return $this->array($path, $options, $value, $rules, $violations);
        }

        // object
        if (is_object($value)) {
            // object-level rule
            if ($rules !== null && !is_array($rules)) {
                return $this->single($path, $options, $value, $rules, $violations);
            }

            return $this->object($path, $options, $value, $rules, $violations);
        }

        throw new UnexpectedValueException(sprintf('Value of type %s cannot be validated.', get_debug_type($value)));
    }

    private function list(
        array $path,
        Options $options,
        array $values,
        EachX $rules,
        array &$violations,
    ): array {
        $normalized = [];
        foreach ($values as $key => $value) {
            $normalized[$key] = $this->traverse([...$path, $key], $options, $value, $rules->rules, $violations);
        }

        return $normalized;
    }

    /**
     * @param string[] $path
     * @param array<string, Rule> $rules
     */
    private function array(
        array $path,
        Options $options,
        array $values,
        array|null $rules,
        array &$violations,
    ): array {
        $rules = $rules ?: [];
        $normalized = [];
        $keys = array_unique(array_merge(array_keys($rules), array_keys($values)));

        foreach ($keys as $key) {
            $normalized[$key] = $this->traverse(
                [...$path, $key],
                $options,
                $values[$key] ?? null,
                $rules[$key] ?? null,
                $violations,
            );
        }

        return $normalized;
    }

    /**
     * @param string[] $path
     * @param array<string, Rule>|null $rules
     * @throws ValueRuleViolation
     */
    private function object(
        array $path,
        Options $options,
        object $value,
        array|null $rules,
        array &$violations,
    ): array {
        $rules = $rules ?: [];

        if ($value instanceof stdClass) {
            return $this->array($path, $options, (array) $value, $rules, $violations);
        }

        // normalized objects are represented as associative arrays (key-value pairs)
        $normalized = [];
        $rc = new ReflectionClass($value);
        $tuples = array_map(static fn (ReflectionProperty $rp) => [$rp->getName(), $rp->getValue($value)], $rc->getProperties());
        $pairs = array_combine(array_column($tuples, 0), array_column($tuples, 1));
        $keys = array_unique(array_merge(array_keys($rules), array_keys($pairs)));

        foreach ($keys as $key) {
            $rule = $rules[$key] ?? null;

            // check if the rule is in attributes
            if ($rule === null) {
                if (!$rc->hasProperty($key)) {
                    continue;
                }

                $property = $rc->getProperty($key);
                $attributes = $property->getAttributes();
                foreach ($attributes as $attribute) {
                    $instance = $attribute->newInstance();
                    if (!$instance instanceof Rule) {
                        continue;
                    }

                    // TODO: support for multiple rules
                    $rule = $instance;

                    break;
                }
            }

            $normalized[$key] = $this->traverse(
                [...$path, $key],
                $options,
                $pairs[$key] ?? null,
                $rule,
                $violations,
            );
        }

        return $normalized;
    }

    /**
     * @param string[] $path
     * @throws ValueRuleViolation
     */
    private function single(
        array $path,
        Options $options,
        object|array|string|int|float|bool|null $value,
        Rule|null $rule,
        array &$violations,
    ): object|array|string|int|float|bool|null {
        if ($rule === null) {
            if ($options->strict) {
                throw new LogicException(
                    message: 'Validation rule is not configured.',
                    path: $path,
                );
            }

            return $value;
        }

        if ($rule instanceof Normalizable) {
            try {
                foreach ($rule->normalizers() as $normalize) {
                    $value = $normalize($value);
                }
            } catch (NormalizationException $e) {
                $this->violation($violations, $path, $e->getMessage(), $options);

                return null;
            }
        }

        $validation = $rule->validator();
        $validator = $this->registry->has($validation)
            ? $this->registry->get($validation)
            : new $validation();

        try {
            $validator($value, $rule);
        } catch (ValueRuleViolation $e) {
            $this->violation($violations, $path, $e->getMessage(), $options);

            return null;
        }

        return $value;
    }

    private function andX(
        array $path,
        Options $options,
        mixed $value,
        AndX $rules,
        array &$violations,
    ): array|string|int|float|bool|null {
        $normalized = null;
        foreach ($rules->rules as $rule) {
            $normalized = $this->traverse($path, $options, $value, $rule, $violations);
        }

        return $normalized;
    }

    private function orX(
        array $path,
        Options $options,
        mixed $value,
        OrX $rules,
        array &$violations,
    ): array|string|int|float|bool|null {
        foreach ($rules->rules as $rule) {
            try {
                $downstream = [];
                $normalized = $this->traverse($path, $options, $value, $rule, $downstream);

                if (count($downstream) === 0) {
                    return $normalized;
                }
            } catch (PropertyRuleViolation|ValueRuleViolation) {
                continue;
            }
        }

        $this->violation($violations, $path, 'Value does not match any of the configured rules.', $options);

        return null;
    }

    private function optionalX(
        array $path,
        Options $options,
        mixed $value,
        OptionalX $rules,
        array &$violations,
    ): array|string|int|float|bool|null {
        return $this->traverse($path, $options, $value, $rules->rules, $violations);
    }

    private function violation(array &$violations, array $path, string $message, Options $options): void
    {
        if ($options->throw) {
            throw new PropertyRuleViolation(
                message: $message,
                path: $path,
            );
        }

        $violations[] = new Violation(path: $path, message: $message);
    }
}
