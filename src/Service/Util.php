<?php

declare(strict_types=1);

namespace App\Service;

use function preg_replace;

class Util
{
    public static function onlyDigits(string $value): string
    {
        return preg_replace('/\D/', '', $value);
    }
}
