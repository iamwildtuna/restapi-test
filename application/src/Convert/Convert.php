<?php
declare(strict_types=1);

namespace App\Convert;

abstract class Convert
{
    protected array $map = [];
    protected array $attributes = [];

    public function getParameters(iterable $headers): iterable
    {
        foreach ($headers as $header => $value) {
            $headerOutput = $this->map[$header] ?? '';
            if ($headerOutput !== '') {
                $this->attributes[$headerOutput] = $value;
            }
        }

        return $this->attributes;
    }
}