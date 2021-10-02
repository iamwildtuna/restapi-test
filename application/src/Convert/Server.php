<?php
declare(strict_types=1);

namespace App\Convert;

class Server extends Convert
{
    public const HTTP_X_FORWARDED_FOR = 'HTTP_X_FORWARDED_FOR';
    public const HTTP_X_REAL_IP = 'HTTP_X_REAL_IP';
    public const REMOTE_ADDR = 'REMOTE_ADDR';
}