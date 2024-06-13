<?php

declare(strict_types=1);

namespace App\Message;

use App\Contract\Dictionary\ParserType;
use App\Contract\Message\AsyncMessageInterface;

class SearchRequestMessage implements AsyncMessageInterface
{
    public function __construct(
        public readonly ParserType $parser,
        public readonly int $requestId,
    ) {
    }
}
