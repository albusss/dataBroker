<?php

declare(strict_types=1);

namespace App\Contract\Parser;

use App\DTO\ParserResponseDTO;

interface ParserInterface
{
    public function getName(): string;

    public function parse(): ?ParserResponseDTO;
}
