<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MatchGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use function preg_replace;
use function substr;


#[ORM\Entity(repositoryClass: MatchGroupRepository::class)]
#[ApiResource]
/*#[Broadcast] // Chat*/
class MatchGroupEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $shortId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $numPlayersRequired = null;

    #[ORM\ManyToMany(targetEntity: PlayerEntity::class, inversedBy: 'matchGroups')]
    private Collection $players;

    #[ORM\OneToMany(mappedBy: 'MatchGroup', targetEntity: MessageEntity::class)]
    private Collection $messages;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->messages = new ArrayCollection();

        $uuid = Uuid::v4();
        $shortId = substr(preg_replace('/[^a-zA-Z0-9]/', '', $uuid->toBase32()), 0, 6);
        $this->setShortId($shortId);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortId(): ?string
    {
        return $this->shortId;
    }

    public function getUrl(): string
    {
        // get the base URL of the app and add the shortId
        return $_SERVER['HTTP_HOST'] . '/' . $this->shortId;
    }

    public function setShortId(string $shortId): static
    {
        $this->shortId = $shortId;

        return $this;
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getNumPlayersRequired(): ?int
    {
        return $this->numPlayersRequired;
    }

    public function setNumPlayersRequired(int $numPlayersRequired): static
    {
        $this->numPlayersRequired = $numPlayersRequired;

        return $this;
    }

    /**
     * @return Collection<int, PlayerEntity>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(PlayerEntity $player): static
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
        }

        return $this;
    }

    public function removePlayer(PlayerEntity $player): static
    {
        $this->players->removeElement($player);

        return $this;
    }

    /**
     * @return Collection<int, MessageEntity>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(MessageEntity $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setMatchGroup($this);
        }

        return $this;
    }

    public function removeMessage(MessageEntity $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getMatchGroup() === $this) {
                $message->setMatchGroup(null);
            }
        }

        return $this;
    }
}
