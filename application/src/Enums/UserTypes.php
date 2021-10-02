<?php
declare(strict_types=1);

namespace App\Enums;

class UserTypes extends Enum
{
    public const CLIENT = 0;
    public const ADMIN = 1;
    public static array $map = [
        self::CLIENT => 'client',
        self::ADMIN  => 'admin'
    ];
}