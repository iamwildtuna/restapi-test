<?php
declare(strict_types=1);

namespace App\Enums;

class Systems extends Enum
{
    public const SYSTEM1 = 0;
    public const SYSTEM2 = 1;
    public static array $map = [
        self::SYSTEM1    => 'System1',
        self::SYSTEM2 => 'System2',
    ];
}