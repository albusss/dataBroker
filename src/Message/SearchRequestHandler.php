<?php

declare(strict_types=1);

namespace App\Message;

use App\DTO\ParserRequestDTO;
use App\Entity\SearchResult;
use App\Exception\NotFoundException;
use App\Repository\SearchRequestRepository;
use App\Repository\SearchResultRepository;
use App\Service\ParserCreator;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SearchRequestHandler
{
    public function __construct(
        private readonly ParserCreator $parserCreator,
        private readonly SearchRequestRepository $searchRequestRepository,
        private readonly SearchResultRepository $searchResultRepository,
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

        $response = $parser->parse(new ParserRequestDTO(
            $searchRequest->getFirstName(),
            $searchRequest->getLastName(),
            $searchRequest->getCity(),
            $searchRequest->getState(),
        ));

        $searchResult = (new SearchResult())
            ->setSearchRequest($searchRequest)
            ->setParserName($parser->getName())
            ->setCreatedAt(new DateTimeImmutable());

        if (!$response) {
            $searchResult->setFullName('No data or error. Please check manually.');
        } else {
            $searchResult
                ->setFullName($response->fullName)
                ->setAddress($response->address)
                ->setLink($response->link)
                ->setAge($response->age);
        }

        $this->searchResultRepository->save($searchResult);
    }
}
