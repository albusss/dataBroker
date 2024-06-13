<?php

namespace App\Entity;

use App\Contract\Dictionary\SearchRequestStatusType;
use App\Repository\SearchRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'search_requests')]
#[ORM\Index(columns: ['status'], name: 'idx_status')]
#[ORM\Entity(repositoryClass: SearchRequestRepository::class)]
class SearchRequest extends AbstractEntity
{
    #[ORM\Column(name: 'status', type: Types::STRING, length: 25, nullable: false, enumType: SearchRequestStatusType::class)]
    private SearchRequestStatusType $status;

    #[ORM\Column(name: 'first_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(name: 'last_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(name: 'state', type: Types::STRING, length: 255, nullable: true)]
    private ?string $state = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'searchRequests', orphanRemoval: true)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'searchRequest', targetEntity: SearchResult::class, orphanRemoval: true)]
    private Collection $searchResults;

    public function __construct()
    {
        $this->users         = new ArrayCollection();
        $this->searchResults = new ArrayCollection();
    }

    public function getStatus(): SearchRequestStatusType
    {
        return $this->status;
    }

    public function setStatus(SearchRequestStatusType $status): self
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

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserInterface $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function getSearchResults(): Collection
    {
        return $this->searchResults;
    }
}
