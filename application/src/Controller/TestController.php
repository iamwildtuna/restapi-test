<?php
declare(strict_types=1);

namespace App\Controller;

use App\Enums\Systems;
use App\Enums\UserTypes;
use App\Error\ApiError;
use App\Exception\ApiException;
use App\Security\Jwt\SecureHash;
use App\Security\Jwt\Token;
use Firebase\JWT\JWT;
use App\Message\TestRabbit;
use App\Response\ApiResponse;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use App\Security\TokenAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/test")
 */
class TestController extends AbstractController
{
    /**
     * @Route("/http")
     *
     * @param  ApiResponse  $response
     * @return Response
     *
     */
    public function http(ApiResponse $response): Response
    {
        return $response->getSuccess(['message' => 'Закрытая зона http basic auth']);
    }

    /**
     * @Route("/ping")n
     *
     * @param  ApiResponse  $response
     * @return Response
     */
    public function ping(ApiResponse $response): Response
    {
        return $response->getSuccess(['message' => 'Привет!']);
    }

    /**
     * @Route("/amqp")
     *
     * @param  ApiResponse  $response
     * @return Response
     */
    public function amqp(ApiResponse $response): Response
    {
        $message = new TestRabbit('Текст сообщения в кролик');
        $this->dispatchMessage($message);

        return $response->getSuccess('Сообщение отправлено!');
    }

    /**
     * @Route("/jwt/create")
     *
     * @param ApiResponse $response
     * @param Request $request
     * @param TokenAuthenticator $tokenAuthenticator
     * @return Response
     */
    public function createJwt(ApiResponse $response, Request $request, Token $token): Response
    {
        $attributes = SecureHash::getAttributesByRequest($request);

        $payload = [
            'iss' => Systems::$map[Systems::SYSTEM1],
            'sub' => 1234,
            'aud' => 1,
            'jti' => Uuid::uuid4()->toString(),
            'exp' => time() + 10000,
            'nbf' => time(),
            'iat' => time(),
            'user_type' => UserTypes::$map[UserTypes::CLIENT],
            'fio' => 'Иванов Сергей Петрович',
            'secure_hash' => SecureHash::encode($attributes),
            'rights' => ['read', 'write']
        ];

        $jwt = $this->getParameter('jwt');

        if (!$token->setSession($payload['jti'], $attributes)) {
            throw new ApiException(ApiError::INTERNAL_SERVER_ERROR);
        }

        $jwt = JWT::encode(
            $payload,
            base64_decode($jwt['private']),
            'RS256'
        );

        return $response->getSuccess(['token' => $jwt]);
    }

    /**
     * @Route("/jwt/auth")
     *
     * @param  ApiResponse  $response
     * @return Response
     */
    public function testAuth(ApiResponse $response): Response
    {
        $user = $this->getUser();

        $message = 'Не удалось получить данные пользователя';

        if (!is_null($user)) {
            $message = 'Авторизованная зона: ' . $user->getUsername();
        }

        return $response->getSuccess(['message' => $message]);
    }

    /**
     * @Route("/redis")
     *
     * @param  ApiResponse  $response
     * @return Response
     */
    public function testRedis(ApiResponse $response): Response
    {
        $redisClient = RedisAdapter::createConnection($this->getParameter('redis.url'));

        $message = 'Невозможно добавить данные в редис';

        if ($redisClient->set('test_key', 'test_value')) {
            $message = 'Данные в редис успешно добавлены: '.$redisClient->get('test_key');
        }

        return $response->getSuccess(['message' => $message]);
    }
}