<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\SlugTrait;
use App\Entity\Trait\UpdatedAtTrait;
use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    use SlugTrait;

    public const REVIEWED_TRANSITION= 'to_review';
    public const PUBLISH_TRANSITION = 'publish';
    public const REJECT_TRANSITION = 'reject';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'subjects')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $owner_id = null;

    #[ORM\ManyToOne(inversedBy: 'talks')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $speacker = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $duration = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'subjects', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Events $events = null;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: SubjectLike::class)]
    private Collection $likes;

    #[ORM\Column]
    private ?bool $is_presented = false;

    #[ORM\Column]
    private ?string $status = 'draft';

    #[ORM\OneToMany(mappedBy: 'subjects', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getOwnerId(): ?User
    {
        return $this->owner_id;
    }

    public function setOwnerId(?User $owner_id): static
    {
        $this->owner_id = $owner_id;

        return $this;
    }

    public function getSpeacker(): ?User
    {
        return $this->speacker;
    }

    public function setSpeacker(?User $speacker): static
    {
        $this->speacker = $speacker;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getEvents(): ?Events
    {
        return $this->events;
    }

    public function setEvents(?Events $events): static
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @return Collection<int, SubjectLike>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(SubjectLike $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setSubject($this);
        }

        return $this;
    }

    public function removeLike(SubjectLike $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getSubject() === $this) {
                $like->setSubject(null);
            }
        }

        return $this;
    }

    public function isIsPresented(): ?bool
    {
        return $this->is_presented;
    }

    public function setIsPresented(bool $is_presented): static
    {
        $this->is_presented = $is_presented;

        return $this;
    }

    /**
     * @return bool
     *              savoir si le sujet est liké par un user
     */
    public function isLikedByUser(User $user): bool
    {
        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) {
                return true;
            }
        }

        return false;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setSubjects($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getSubjects() === $this) {
                $comment->setSubjects(null);
            }
        }

        return $this;
    }
}
