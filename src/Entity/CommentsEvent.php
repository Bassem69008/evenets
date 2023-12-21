<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\CommentsEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsEventRepository::class)]
class CommentsEvent
{
    use CreatedAtTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commentsEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Events $event = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'commentsEvents')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $commentsEvents;

    #[ORM\Column(nullable: true)]
    private ?bool $is_active = null;

    public function __construct()
    {
        $this->commentsEvents = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEvent(): ?Events
    {
        return $this->event;
    }

    public function setEvent(?Events $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCommentsEvents(): Collection
    {
        return $this->commentsEvents;
    }

    public function addCommentsEvent(self $commentsEvent): static
    {
        if (!$this->commentsEvents->contains($commentsEvent)) {
            $this->commentsEvents->add($commentsEvent);
            $commentsEvent->setParent($this);
        }

        return $this;
    }

    public function removeCommentsEvent(self $commentsEvent): static
    {
        if ($this->commentsEvents->removeElement($commentsEvent)) {
            // set the owning side to null (unless already changed)
            if ($commentsEvent->getParent() === $this) {
                $commentsEvent->setParent(null);
            }
        }

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(?bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }
}
