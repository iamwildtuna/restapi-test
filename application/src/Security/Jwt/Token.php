<?php
declare(strict_types=1);

namespace App\Security\Jwt;

use App\Exception\ApiException;
use App\Helper\JsonHelper;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class Token
{
    public const REDIS_KEY_CONST = 'app-auth';
    public const ALGORITHM = 'RS256';
    protected bool $check;
    protected string $public;
    protected string $redisUrl;
    protected int $ttl;

    /**
     * TokenAuthenticator constructor.
     *
     * @param  array  $params
     */
    public function __construct(array $params)
    {
        $this->public = (string) $params['public'];
        $this->check = (bool) $params['check'];
        $this->redisUrl = (string) $params['redis'];
        $this->ttl = (int) $params['ttl'];
    }

    /**
     * @param  string  $jti
     *
     * @return array
     * @throws ApiException
     */
    public function getSession(string $jti): array
    {
        $redisKey = $this->getRedisKey('session', $jti);

        $json = RedisAdapter::createConnection($this->redisUrl)
            ->get($redisKey);

        return empty($json) ? [] : JsonHelper::decode($json);
    }

    /**
     * @param  string  $jti

     * @return bool
     * @throws ApiException
     */
    public function setSession(string $jti, $data): bool
    {
        $redisKey = $this->getRedisKey('session', $jti);

        $result = RedisAdapter::createConnection($this->redisUrl)
            ->set($redisKey, JsonHelper::encode($data), $this->ttl);

        return $result;
    }

    /**
     * @return bool
     */
    public function getCheck(): bool
    {
        return $this->check;
    }

    /**
     * @return string
     */
    public function getPublic(): string
    {
        return $this->public;
    }

    /**
     * Получение ключа в редис, для получения информации о токене.
     *
     * @param  string  $type
     * @param  string  $key
     *
     * @return string
     */
    protected function getRedisKey(string $type, string $key): string
    {
        return self::REDIS_KEY_CONST.':'.$type.':'.$key;
    }
}