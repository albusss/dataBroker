<?php

declare(strict_types=1);

namespace App\Contract\Parser;

use App\DTO\ParserRequestDTO;
use App\DTO\ParserResponseDTO;

interface ParserInterface
{
    public function getName(): string;

    /**
     * @return ParserResponseDTO[]
     */
    public function parse(ParserRequestDTO $request): array;
}
