<?php

class Arguments
{
    public static function notEmpty($value, string $name = 'Argument'): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException("{$name} cannot be empty");
        }
    }

    public static function notNull($value, string $name = 'Argument'): void
    {
        if ($value === null) {
            throw new InvalidArgumentException("{$name} cannot be null");
        }
    }

    public static function isString($value, string $name = 'Argument'): void
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException("{$name} must be a string");
        }
    }

    public static function stringNotEmpty(string $value, string $name = 'Argument'): void
    {
        self::isString($value, $name);
        if (trim($value) === '') {
            throw new InvalidArgumentException("{$name} cannot be empty string");
        }
    }
}

