<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SearchRequestHandler
{
    public function __construct(
    ) {
    }

    public function __invoke(SearchRequestMessage $message): void
    {
    }
}
