<?php
declare(strict_types=1);

namespace App\Security\Jwt;

use App\Convert\Headers;
use App\Convert\Server;
use Symfony\Component\HttpFoundation\Request;

class SecureHash
{
    public static function encode(array $attributes): string
    {
        return md5(implode('', $attributes));
    }

    public static function getAttributesByRequest(Request $request): array
    {
        $arguments = static::getIpAddresses(
            $request->server->get(Server::REMOTE_ADDR),
            $request->server->get(Server::HTTP_X_REAL_IP),
            $request->server->get(Server::HTTP_X_FORWARDED_FOR)
        );
        $arguments['user_agent'] = $request->headers->get(Headers::USER_AGENT);

        return $arguments;
    }

    public static function getIpAddresses(
        string $remote_ip,
        ?string $realIp = null,
        ?string $forwarded_ip = null
    ): array {
        return [
            'remote_ip' => $remote_ip,
            'real_ip' => $realIp ?? '',
            'forwarded_ip' => $forwarded_ip ?? '',
        ];
    }
}