<?php

declare(strict_types=1);

namespace App\DTO;

class ParserResponseDTO
{
    public function __construct(
        public readonly ?string $fullName,
        public readonly ?string $address,
        public readonly ?string $link,
        public readonly ?string $age,
    ) {
    }
}
