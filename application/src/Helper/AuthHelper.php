<?php
declare(strict_types=1);

namespace App\Helper;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

use App\Error\ApiError;
use App\Exception\ApiException;
use App\Security\Jwt\Token;

use stdClass;
use Exception;

class AuthHelper
{
    /**
     * @param  string  $token
     * @param  string  $public_key
     *
     * @return stdClass
     *
     * @throws ApiException
     */
    public static function getPayload(string $token, string $public_key): stdClass
    {
        try {
            return JWT::decode($token, base64_decode($public_key), [Token::ALGORITHM]);
        } catch (ExpiredException $exception) {
            throw new ApiException(ApiError::TOKEN_EXPIRED);
        } catch (BeforeValidException | Exception $exception) {
            throw new ApiException(ApiError::TOKEN_INVALID);
        }
    }

    /**
     * Проверяет не перехвачен ли JWT токен.
     *
     * @param  bool  $needCheck
     * @param  string  $secure_hash_request
     * @param  string  $secure_hash_token
     *
     * @return bool
     */
    public static function checkTokenIntercepted(bool $needCheck, string $secure_hash_request, string $secure_hash_token): bool
    {
        return $needCheck && $secure_hash_request !== $secure_hash_token;
    }
}