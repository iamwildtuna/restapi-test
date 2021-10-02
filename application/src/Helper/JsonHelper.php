<?php
declare(strict_types=1);

namespace App\Helper;

use JsonException;
use App\Error\ApiError;
use App\Exception\ApiException;

class JsonHelper
{
    /**
     * @param  string  $json
     * @param  bool  $associative
     * @param  int  $depth
     *
     * @return array
     * @throws ApiException
     */
    public static function decode(string $json, bool $associative = true, int $depth = 512): array
    {
        try {
            return json_decode($json, $associative, $depth, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ApiException(ApiError::JSON_SYNTAX_ERROR, [], $exception);
        }
    }

    /**
     * @param  mixed  $value
     *
     * @return string
     * @throws ApiException
     */
    public static function encode($value): string
    {
        try {
            return json_encode($value, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ApiException(ApiError::JSON_SYNTAX_ERROR, [], $exception);
        }
    }
}