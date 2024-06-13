<?php

declare(strict_types=1);

namespace App\Message;

use App\Contract\Dictionary\ParserType;
use App\Contract\Message\AsyncMessageInterface;

/**
 * @see SearchRequestHandler
 */
class SearchRequestMessage implements AsyncMessageInterface
{
    public function __construct(
        public readonly ParserType $parserType,
        public readonly int $requestId,
    ) {
    }
}
