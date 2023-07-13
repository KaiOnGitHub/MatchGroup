<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class PlayerEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\ManyToMany(targetEntity: MatchGroupEntity::class, mappedBy: 'players')]
    private Collection $matchGroups;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $authToken = null;

    #[ORM\Column(nullable: true)]
    private ?bool $wantsNotifications = null;

    public function __construct()
    {
        $this->matchGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, MatchGroupEntity>
     */
    public function getMatchGroups(): Collection
    {
        return $this->matchGroups;
    }

    public function addMatchGroup(MatchGroupEntity $matchGroup): static
    {
        if (!$this->matchGroups->contains($matchGroup)) {
            $this->matchGroups->add($matchGroup);
            $matchGroup->addPlayer($this);
        }

        return $this;
    }

    public function removeMatchGroup(MatchGroupEntity $matchGroup): static
    {
        if ($this->matchGroups->removeElement($matchGroup)) {
            $matchGroup->removePlayer($this);
        }

        return $this;
    }

    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    public function setAuthToken(?string $authToken): static
    {
        $this->authToken = $authToken;

        return $this;
    }

    public function isWantsNotifications(): ?bool
    {
        return $this->wantsNotifications;
    }

    public function setWantsNotifications(?bool $wantsNotifications): static
    {
        $this->wantsNotifications = $wantsNotifications;

        return $this;
    }
}
