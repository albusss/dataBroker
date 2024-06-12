<?php

namespace App\Entity;

use App\Contract\Dictionary\SearchStatusType;
use App\Repository\SearchRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'search_requests')]
#[ORM\Index(columns: ['user_id'], name: 'idx_user')]
#[ORM\Index(columns: ['status'], name: 'idx_status')]
#[ORM\Entity(repositoryClass: SearchRequestRepository::class)]
class SearchRequest extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'searchRequests')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 25, nullable: false, enumType: SearchStatusType::class)]
    private SearchStatusType $status;

    #[ORM\Column(name: 'first_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $firstName;

    #[ORM\Column(name: 'last_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $lastName;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 255, nullable: true)]
    private ?string $city;

    #[ORM\Column(name: 'state', type: Types::STRING, length: 255, nullable: true)]
    private ?string $state;

    #[ORM\OneToMany(mappedBy: 'searchRequest', targetEntity: SearchResult::class, orphanRemoval: true)]
    private Collection $searchResults;

    public function __construct()
    {
        $this->searchResults = new ArrayCollection();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): SearchStatusType
    {
        return $this->status;
    }

    public function setStatus(SearchStatusType $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getSearchResults(): Collection
    {
        return $this->searchResults;
    }
}
