<?php

declare(strict_types=1);

namespace Norvica\Validation;

use Norvica\Validation\Exception\LogicException;
use Norvica\Validation\Exception\PropertyRuleViolation;
use Norvica\Validation\Instruction\EachX;
use Norvica\Validation\Rule\Rule;
use Norvica\Validation\Exception\ValueRuleViolation;
use Norvica\Validation\Normalizer\Normalizable;
use ReflectionClass;
use stdClass;
use UnexpectedValueException;

final class Validator
{
    /**
     * @throws ValueRuleViolation
     */
    public function validate(mixed $value, Rule|EachX|array|null $rules = null, bool $strict = true): void
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
        Rule|EachX|array|null $rules = null,
    ): void {
        // scalar
        if ($value === null || is_scalar($value)) {
            $this->single($path, $strict, $value, $rules);

            return;
        }

        // list
        if (is_array($value) && array_is_list($value)) {
            $rules instanceof EachX
                ? $this->list($path, $strict, $value, $rules)
                : $this->single($path, $strict, $value, $rules);

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

        foreach ($values as $key => $value) {
            $this->traverse([...$path, $key], $strict, $value, $rules[$key] ?? null);
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
        foreach ($rc->getProperties() as $rp) {
            $key = $rp->getName();
            $rule = $rules[$key] ?? null;
            if ($rule === null) {
                $attributes = $rp->getAttributes();
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

            $this->traverse([...$path, $key], $strict, $rp->getValue($value), $rule);
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
            foreach ($rule->normalizers() as $sanitize) {
                $value = $sanitize($value);
            }
        }

        $class = $rule->validator();
        $validator = new $class(); // TODO: allow PSR container integration

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
}
