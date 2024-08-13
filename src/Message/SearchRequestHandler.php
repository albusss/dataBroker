<?php

declare(strict_types=1);

namespace App\Message;

use App\DTO\ParserRequestDTO;
use App\Repository\SearchRequestRepository;
use App\Service\ParserQueueClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

use function sprintf;

#[AsMessageHandler]
class SearchRequestHandler
{
    public function __construct(
        private readonly SearchRequestRepository $searchRequestRepository,
        private readonly ParserQueueClient $parserQueueClient,
        private readonly LoggerInterface $parserLogger,
    ) {
    }

    public function __invoke(SearchRequestMessage $message): void
    {
        $searchRequest = $this->searchRequestRepository->find($message->requestId);

        if (!$searchRequest) {
            $this->parserLogger->error(sprintf('Request #%d not found', $message->requestId));

            return;
        }

        $parserName = $message->parserType->name;

        try {
            $this->parserQueueClient->pushRequest(new ParserRequestDTO(
                $parserName,
                $searchRequest->getFirstName(),
                $searchRequest->getLastName(),
                $searchRequest->getCity(),
                $searchRequest->getState(),
            ));
        } catch (Throwable $e) {
            $this->parserLogger->error(
                sprintf('#%d [%s]: %s', $searchRequest->getId(), $parserName, $e->getMessage()),
            );
        }
    }
}
