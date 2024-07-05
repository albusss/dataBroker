<?php

declare(strict_types=1);

namespace App\Message;

use App\DTO\ParserRequestDTO;
use App\Exception\NotFoundException;
use App\Repository\SearchRequestRepository;
use App\Service\ParserCreator;
use App\Service\SearchResultCreator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

use function sprintf;

#[AsMessageHandler]
class SearchRequestHandler
{
    public function __construct(
        private readonly SearchRequestRepository $searchRequestRepository,
        private readonly ParserCreator $parserCreator,
        private readonly SearchResultCreator $searchResultCreator,
        private readonly LoggerInterface $parserLogger,
    ) {
    }

    public function __invoke(SearchRequestMessage $message): void
    {
        try {
            $searchRequest = $this->searchRequestRepository->find($message->requestId);

            if (!$searchRequest) {
                throw new NotFoundException('Request #' . $message->requestId . ' not found');
            }

            $parser = $this->parserCreator->create($message->parserType);
        } catch (NotFoundException $e) {
            $this->parserLogger->error($e->getMessage());

            return;
        }

        $parserName = $parser->getName();

        try {
            $response = $parser->parse(new ParserRequestDTO(
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

        if (empty($response)) {
            $this->searchResultCreator->create(
                $searchRequest,
                $parserName,
                'No data or error. Please check manually.',
            );

            return;
        }

        foreach ($response as $result) {
            $this->searchResultCreator->create(
                $searchRequest,
                $parserName,
                $result->fullName,
                $result->address,
                $result->link,
                $result->age,
            );
        }
    }
}
