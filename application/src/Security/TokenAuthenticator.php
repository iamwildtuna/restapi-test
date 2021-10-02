<?php
declare(strict_types=1);

namespace App\Security;

use App\Error\ApiError;
use App\Response\ApiResponse;
use App\Enums\Systems;
use App\Helper\AuthHelper;
use App\Security\Jwt\SecureHash;
use App\Security\Jwt\Token;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exception\ApiException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private int $error;
    private Token $token;

    /**
     * TokenAuthenticator constructor.
     *
     * @param  Token  $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @param  Request  $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization');
    }

    /**
     * @param  Request  $request
     *
     * @return array
     * @throws ApiException
     */
    public function getCredentials(Request $request): array
    {
        $token_raw = $request->headers->get('Authorization');

        if (strpos($token_raw, 'Bearer ') === false) {
            throw new ApiException(ApiError::TOKEN_INVALID);
        }

        [$type, $token] = explode(' ', $token_raw);

        $attributes = SecureHash::getAttributesByRequest($request);

        return [
            'type'        => $type,
            'token'       => $token,
            'secure_hash' => SecureHash::encode($attributes),
        ];
    }

    /**
     * @param  mixed  $credentials
     * @param  UserProviderInterface  $userProvider
     *
     * @return User
     *
     * @throws ApiException
     */
    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        $needCheckToken = $this->token->getCheck();
        $publicKey = $this->token->getPublic();

        $token = $credentials['token'] ?? null;
        $secureHash = $credentials['secure_hash'];

        if (empty($token)) {
            throw new ApiException(ApiError::TOKEN_NO_PASSED);
        }

        $payload = AuthHelper::getPayload($token, $publicKey);

        if (!Systems::hasValue($payload->iss)) {
            throw new ApiException(ApiError::TOKEN_PUBLISHER_NOT_FOUND);
        }

        if (empty($this->token->getSession($payload->jti))) {
            throw new ApiException(ApiError::TOKEN_REVOKED);
        }

        if (AuthHelper::checkTokenIntercepted($needCheckToken, $secureHash, $payload->secure_hash)) {
            throw new ApiException(ApiError::TOKEN_INTERCEPTED);
        }

        $user = new User($payload);
        $user->setRoles(['ROLE_USER']);

        return $user;
    }

    /**
     * @param  mixed  $credentials
     * @param  UserInterface  $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $credentials['type'] === 'Bearer';
    }

    /**
     * @param  Request  $request
     * @param  TokenInterface  $token
     * @param  string  $providerKey
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        return null;
    }

    /**
     * @param  Request  $request
     * @param  AuthenticationException  $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return (new ApiResponse($request))->getError($this->getActualException($exception));
    }

    /**
     * @param  Request  $request
     * @param  AuthenticationException|null  $authException
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        if (is_null($authException)) {
            $authException = new AuthenticationException('Системная ошибка');
        }

        return (new ApiResponse($request))->getError($this->getActualException($authException));
    }

    /**
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }

    private function getActualException(AuthenticationException $authException)
    {
        return empty($this->error) ? $authException : new ApiException($this->error);
    }
}