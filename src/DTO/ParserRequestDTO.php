<?php

declare(strict_types=1);

namespace App\DTO;

class ParserRequestDTO
{
    public function __construct(
        public readonly string $parserName,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $city,
        public readonly ?string $state,
    ) {
    }
}
