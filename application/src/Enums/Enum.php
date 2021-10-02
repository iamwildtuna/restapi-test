<?php
declare(strict_types=1);

namespace App\Enums;

use App\Error\ApiError;
use App\Exception\ApiException;

abstract class Enum
{
    protected static array $map;

    /**
     * Возвращает тип пользователя по Int представлению
     * @param  int  $type
     *
     * @return string
     * @throws ApiException
     */
    public static function getValueByKey(int $type): string
    {
        if (self::hasKey($type)) {
            return static::$map[$type];
        }

        throw new ApiException(ApiError::NOT_FOUND, [static::class]);
    }

    /**
     * Проверяет что значение присутствует в карте
     *
     * @param  string  $value
     *
     * @return bool
     */
    public static function hasValue(string $value): bool
    {
        return in_array($value, static::$map, true);
    }

    /**
     * Проверяет что значение ключ присутствует в карте.
     *
     * @param  int  $type
     *
     * @return bool
     */
    public static function hasKey(int $type): bool
    {
        return isset(static::$map[$type]);
    }

    /**
     * Возвращает список ключей
     *
     * @return array
     */
    public static function getKeys(): array
    {
        return array_keys(static::$map);
    }
}