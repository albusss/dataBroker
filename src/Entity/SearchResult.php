<?php

namespace App\Entity;

use App\Repository\SearchResultRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'search_results')]
#[ORM\Index(columns: ['search_request_id'], name: 'idx_search_request')]
#[ORM\Index(columns: ['parser_name'], name: 'idx_parser_name')]
#[ORM\Entity(repositoryClass: SearchResultRepository::class)]
class SearchResult extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: SearchRequest::class, inversedBy: 'searchResults')]
    #[ORM\JoinColumn(name: 'search_request_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private SearchRequest $searchRequest;

    #[ORM\Column(name: 'parser_name', type: Types::STRING, length: 50, nullable: false)]
    private string $parserName;

    #[ORM\Column(name: 'full_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(name: 'address', type: Types::STRING, length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(name: 'link', type: Types::STRING, length: 500, nullable: true)]
    private ?string $link = null;

    #[ORM\Column(name: 'age', type: Types::STRING, length: 30, nullable: true)]
    private ?string $age = null;

    public function getSearchRequest(): SearchRequest
    {
        return $this->searchRequest;
    }

    public function setSearchRequest(SearchRequest $searchRequest): self
    {
        $this->searchRequest = $searchRequest;

        return $this;
    }

    public function getParserName(): string
    {
        return $this->parserName;
    }

    public function setParserName(string $parserName): self
    {
        $this->parserName = $parserName;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

        return $this;
    }
}
