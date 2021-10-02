<?php
declare(strict_types=1);

namespace App\Convert;

class Headers extends Convert
{
    public const REQUEST_ID = 'Request-Id';
    public const TRACER_ID = 'Tracer-Id';
    public const USER_AGENT = 'User-Agent';
    protected array $map = [
        self::REQUEST_ID      => 'request_id',
        self::TRACER_ID       => 'parent_span_id',
    ];
}