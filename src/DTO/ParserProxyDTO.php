<?php

declare(strict_types=1);

namespace App\DTO;

class ParserProxyDTO
{
    public function __construct(
        public readonly string $host,
        public readonly string $port,
        public readonly string $user,
        public readonly string $password,
    ) {
    }
}
