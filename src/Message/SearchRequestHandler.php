<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\SearchResult;
use App\Exception\NotFoundException;
use App\Repository\SearchRequestRepository;
use App\Repository\SearchResultRepository;
use App\Service\ParserCreator;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SearchRequestHandler
{
    public function __construct(
        private readonly ParserCreator $parserCreator,
        private readonly SearchRequestRepository $searchRequestRepository,
        private readonly SearchResultRepository $searchResultRepository,
    ) {
    }

    public function __invoke(SearchRequestMessage $message): void
    {
        try {
            $searchRequest = $this->searchRequestRepository->find($message->requestId);

            if (!$searchRequest) {
                throw new NotFoundException('Request "' . $message->requestId . '" not found');
            }

            $parser = $this->parserCreator->create($message->parserType);
        } catch (NotFoundException) {
            return;
        }

        $result = $parser->parse();

        $searchResult = (new SearchResult())
            ->setSearchRequest($searchRequest)
            ->setParserName($parser->getName())
            ->setCreatedAt(new DateTimeImmutable());

        if (!$result) {
            $searchResult->setFullName('No data or error. Please check manually.');
        } else {
            $searchResult
                ->setFullName($result->fullName)
                ->setAddress($result->address)
                ->setLink($result->link)
                ->setAge($result->age);
        }

        $this->searchResultRepository->save($searchResult);
    }
}
