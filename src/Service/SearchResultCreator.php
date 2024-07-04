<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SearchRequest;
use App\Entity\SearchResult;
use App\Repository\SearchResultRepository;
use DateTimeImmutable;

class SearchResultCreator
{
    public function __construct(
        private readonly SearchResultRepository $searchResultRepository,
    ) {
    }

    public function create(
        SearchRequest $searchRequest,
        string $parserName,
        string $fullName,
        ?string $address = null,
        ?string $link = null,
        ?string $age = null,
    ): void {
        $this->searchResultRepository->save(
            (new SearchResult())
                ->setSearchRequest($searchRequest)
                ->setParserName($parserName)
                ->setFullName($fullName)
                ->setAddress($address)
                ->setLink($link)
                ->setAge($age)
                ->setCreatedAt(new DateTimeImmutable())
        );
    }
}
