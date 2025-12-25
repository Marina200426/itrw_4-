<?php

class Arguments
{
    /**
     * Проверяет, что значение не пустое
     * @param mixed $value Значение для проверки
     * @param string $name Имя аргумента для сообщения об ошибке
     * @throws InvalidArgumentException
     */
    public static function notEmpty($value, string $name = 'Argument'): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException("{$name} cannot be empty");
        }
    }

    /**
     * Проверяет, что значение не null
     * @param mixed $value Значение для проверки
     * @param string $name Имя аргумента для сообщения об ошибке
     * @throws InvalidArgumentException
     */
    public static function notNull($value, string $name = 'Argument'): void
    {
        if ($value === null) {
            throw new InvalidArgumentException("{$name} cannot be null");
        }
    }

    /**
     * Проверяет, что значение является строкой
     * @param mixed $value Значение для проверки
     * @param string $name Имя аргумента для сообщения об ошибке
     * @throws InvalidArgumentException
     */
    public static function isString($value, string $name = 'Argument'): void
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException("{$name} must be a string");
        }
    }

    /**
     * Проверяет, что строка не пустая
     * @param string $value Значение для проверки
     * @param string $name Имя аргумента для сообщения об ошибке
     * @throws InvalidArgumentException
     */
    public static function stringNotEmpty(string $value, string $name = 'Argument'): void
    {
        self::isString($value, $name);
        if (trim($value) === '') {
            throw new InvalidArgumentException("{$name} cannot be empty string");
        }
    }
}

