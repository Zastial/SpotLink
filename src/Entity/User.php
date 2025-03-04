<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Enum\Role as RoleEnum;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?bool $is_verify = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'creator', cascade: ['persist', 'remove'])]
    private Collection $events;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class)]
    private Collection $favorites;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;
    
    /**
     * @var Collection<int, JwtToken>
     */
    #[ORM\OneToMany(targetEntity: JwtToken::class, mappedBy: 'createdBy')]
    private Collection $jwtTokens;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->jwtTokens = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->is_verify = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isVerify(): ?bool
    {
        return $this->is_verify;
    }

    public function setIsVerify(bool $is_verify): static
    {
        $this->is_verify = $is_verify;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setCreator($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCreator() === $this) {
                $event->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Event $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
        }

        return $this;
    }

    public function removeFavorite(Event $favorite): static
    {
        $this->favorites->removeElement($favorite);

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }
    
    /**
     * @return Collection<int, JwtToken>
     */
    public function getJwtTokens(): Collection
    {
        return $this->jwtTokens;
    }

    public function addJwtToken(JwtToken $jwtToken): static
    {
        if (!$this->jwtTokens->contains($jwtToken)) {
            $this->jwtTokens->add($jwtToken);
            $jwtToken->setCreatedBy($this);
        }

        return $this;
    }

    public function removeJwtToken(JwtToken $jwtToken): static
    {
        if ($this->jwtTokens->removeElement($jwtToken)) {
            // set the owning side to null (unless already changed)
            if ($jwtToken->getCreatedBy() === $this) {
                $jwtToken->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roleName = $this->role ? $this->role->getName() : RoleEnum::USER;
        return ['ROLE_' . $roleName];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials() : void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
