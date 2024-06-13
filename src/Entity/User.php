<?php

namespace App\Entity;

use App\Contract\Entity\EntityInterface;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function array_unique;

#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'uniq_username', columns: ['username'])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'username', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private string $username;

    #[ORM\Column(name: 'password', type: Types::STRING, length: 255, nullable: false)]
    private string $password;

    #[ORM\Column(name: 'roles', type: Types::JSON, nullable: false)]
    private array $roles = [];

    #[ORM\ManyToMany(targetEntity: SearchRequest::class, inversedBy: 'users', orphanRemoval: true)]
    #[ORM\JoinTable(name: 'user_search_requests')]
    private Collection $searchRequests;

    public function __construct()
    {
        $this->searchRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getSearchRequests(): Collection
    {
        return $this->searchRequests;
    }

    public function addSearchRequest(SearchRequest $searchRequest): self
    {
        if (!$this->searchRequests->contains($searchRequest)) {
            $this->searchRequests->add($searchRequest);
        }

        return $this;
    }
}
