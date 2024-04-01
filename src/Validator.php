<?php

declare(strict_types=1);

namespace Norvica\Validation;

use Norvica\Validation\Exception\LogicException;
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
use ReflectionClass;
use ReflectionProperty;
use stdClass;
use TypeError;
use UnexpectedValueException;

final class Validator
{
    private Registry $registry;

    public function __construct(
        Registry|array $registry = [],
    ) {
        $this->registry = is_array($registry) ? new MapRegistry($registry) : $registry;
    }

    /**
     * @throws ValueRuleViolation
     */
    public function validate(mixed $value, Rule|OptionalX|EachX|AndX|OrX|array|null $rules = null, bool $strict = true): void
    {
        $this->traverse([], $strict, $value, $rules);
    }

    /**
     * @param string[] $path
     * @throws ValueRuleViolation
     */
    private function traverse(
        array $path,
        bool $strict,
        mixed $value,
        Rule|OptionalX|EachX|AndX|OrX|array|null $rules = null,
    ): void {
        if ($value === null) {
            if (!$rules instanceof OptionalX) {
                throw new PropertyRuleViolation(
                    message: 'Value is required',
                    path: $path,
                );
            }

            return;
        }

        if ($rules instanceof OptionalX) {
            $this->optionalX($path, $strict, $value, $rules);

            return;
        }

        if ($rules instanceof AndX) {
            $this->andX($path, $strict, $value, $rules);

            return;
        }

        if ($rules instanceof OrX) {
            $this->orX($path, $strict, $value, $rules);

            return;
        }

        // scalar
        if (is_scalar($value)) {
            $this->single($path, $strict, $value, $rules);

            return;
        }

        // list
        if (is_array($value) && array_is_list($value)) {
            match (true) {
                $rules instanceof EachX => $this->list($path, $strict, $value, $rules),
                $rules instanceof Rule => $this->single($path, $strict, $value, $rules),
                default => $this->array($path, $strict, $value, $rules),
            };

            return;
        }

        // associative array
        if (is_array($value)) {
            $this->array($path, $strict, $value, $rules);

            return;
        }

        // object
        if (is_object($value)) {
            $this->object($path, $strict, $value, $rules);

            return;
        }

        throw new UnexpectedValueException(sprintf('Value of type %s cannot be validated.', get_debug_type($value)));
    }

    private function list(array $path, bool $strict, array $values, EachX $rules): void
    {
        foreach ($values as $key => $value) {
            $this->traverse([...$path, $key], $strict, $value, $rules->rules);
        }
    }

    /**
     * @param string[] $path
     * @param array<string, Rule> $rules
     */
    private function array(array $path, bool $strict, array $values, array|null $rules = null): void
    {
        $rules = $rules ?: [];
        $keys = array_merge(array_keys($rules), array_keys($values));

        foreach ($keys as $key) {
            $this->traverse([...$path, $key], $strict, $values[$key] ?? null, $rules[$key] ?? null);
        }
    }

    /**
     * @param string[] $path
     * @param array<string, Rule>|null $rules
     * @throws ValueRuleViolation
     */
    private function object(array $path, bool $strict, object $value, array|null $rules = null): void
    {
        $rules = $rules ?: [];

        if ($value instanceof stdClass) {
            $this->array($path, $strict, (array) $value, $rules);

            return;
        }

        $rc = new ReflectionClass($value);
        $tuples = array_map(static fn (ReflectionProperty $rp) => [$rp->getName(), $rp->getValue($value)], $rc->getProperties());
        $pairs = array_combine(array_column($tuples, 0), array_column($tuples, 1));
        $keys = array_merge(array_keys($rules), array_keys($pairs));

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

            $this->traverse([...$path, $key], $strict, $pairs[$key] ?? null, $rule);
        }
    }

    /**
     * @param string[] $path
     * @throws ValueRuleViolation
     */
    private function single(array $path, bool $strict, array|string|int|float|bool|null $value, Rule|null $rule = null): void
    {
        if ($rule === null) {
            if ($strict) {
                throw new LogicException(
                    message: 'Validation rule is not configured.',
                    path: $path,
                );
            }

            return;
        }

        if ($rule instanceof Normalizable) {
            foreach ($rule->normalizers() as $normalize) {
                $value = $normalize($value);
            }
        }

        $validation = $rule->validator();
        $validator = $this->registry->has($validation)
            ? $this->registry->get($validation)
            : new $validation();

        try {
            $validator($value, $rule);
        } catch (ValueRuleViolation $e) {
            throw new PropertyRuleViolation(
                message: $e->getMessage(),
                code: $e->getCode(),
                previous: $e,
                path: $path,
            );
        }
    }

    private function andX(array $path, bool $strict, mixed $value, AndX $rules): void
    {
        foreach ($rules->rules as $rule) {
            $this->traverse($path, $strict, $value, $rule);
        }
    }

    private function orX(array $path, bool $strict, mixed $value, OrX $rules): void
    {
        foreach ($rules->rules as $rule) {
            try {
                $this->traverse($path, $strict, $value, $rule);

                return;
            } catch (PropertyRuleViolation|ValueRuleViolation) {
                continue;
            }
        }

        throw new PropertyRuleViolation(
            message: 'Value does not match any of the configured rules.',
            path: $path,
        );
    }

    private function optionalX(array $path, bool $strict, mixed $value, OptionalX $rules): void
    {
        $this->traverse($path, $strict, $value, $rules->rules);
    }
}
