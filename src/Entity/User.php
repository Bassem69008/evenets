<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use CreatedAtTrait;
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_CONNECTED = 'ROLE_CNNECTED';
    public const ROLE_BOARD = 'ROLE_BOARD';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $do = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $site = null;

    #[ORM\Column]
    private ?int $rate = 1;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(mappedBy: 'owner_id', targetEntity: Subject::class)]
    private Collection $subjects;

    #[ORM\OneToMany(mappedBy: 'speacker_id', targetEntity: Subject::class)]
    private Collection $talks;

    #[ORM\OneToMany(mappedBy: 'created_by', targetEntity: Events::class, orphanRemoval: true)]
    private Collection $events_created;

    #[ORM\OneToMany(mappedBy: 'updated_by', targetEntity: Events::class)]
    private Collection $events_updated;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SubjectLike::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Subscription::class, orphanRemoval: true)]
    private Collection $subscriptions;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: CommentsEvent::class, orphanRemoval: true)]
    private Collection $commentsEvents;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
        $this->talks = new ArrayCollection();
        $this->events_created = new ArrayCollection();
        $this->events_updated = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->commentsEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_CONNECTED';

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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
            $subject->setOwnerId($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        if ($this->subjects->removeElement($subject)) {
            // set the owning side to null (unless already changed)
            if ($subject->getOwnerId() === $this) {
                $subject->setOwnerId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getTalks(): Collection
    {
        return $this->talks;
    }

    public function addTalk(Subject $talk): static
    {
        if (!$this->talks->contains($talk)) {
            $this->talks->add($talk);
            $talk->setSpeackerId($this);
        }

        return $this;
    }

    public function removeTalk(Subject $talk): static
    {
        if ($this->talks->removeElement($talk)) {
            // set the owning side to null (unless already changed)
            if ($talk->getSpeackerId() === $this) {
                $talk->setSpeackerId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Events>
     */
    public function getEventsCreated(): Collection
    {
        return $this->events_created;
    }

    public function addEventsCreated(Events $eventsCreated): static
    {
        if (!$this->events_created->contains($eventsCreated)) {
            $this->events_created->add($eventsCreated);
            $eventsCreated->setCreatedBy($this);
        }

        return $this;
    }

    public function removeEventsCreated(Events $eventsCreated): static
    {
        if ($this->events_created->removeElement($eventsCreated)) {
            // set the owning side to null (unless already changed)
            if ($eventsCreated->getCreatedBy() === $this) {
                $eventsCreated->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Events>
     */
    public function getEventsUpdated(): Collection
    {
        return $this->events_updated;
    }

    public function addEventsUpdated(Events $eventsUpdated): static
    {
        if (!$this->events_updated->contains($eventsUpdated)) {
            $this->events_updated->add($eventsUpdated);
            $eventsUpdated->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeEventsUpdated(Events $eventsUpdated): static
    {
        if ($this->events_updated->removeElement($eventsUpdated)) {
            // set the owning side to null (unless already changed)
            if ($eventsUpdated->getUpdatedBy() === $this) {
                $eventsUpdated->setUpdatedBy(null);
            }
        }

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
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(SubjectLike $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    public function getDo(): ?string
    {
        return $this->do;
    }

    public function setDo(?string $do): static
    {
        $this->do = $do;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(?string $site): static
    {
        $this->site = $site;

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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setUser($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getUser() === $this) {
                $subscription->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CommentsEvent>
     */
    public function getCommentsEvents(): Collection
    {
        return $this->commentsEvents;
    }

    public function addCommentsEvent(CommentsEvent $commentsEvent): static
    {
        if (!$this->commentsEvents->contains($commentsEvent)) {
            $this->commentsEvents->add($commentsEvent);
            $commentsEvent->setUser($this);
        }

        return $this;
    }

    public function removeCommentsEvent(CommentsEvent $commentsEvent): static
    {
        if ($this->commentsEvents->removeElement($commentsEvent)) {
            // set the owning side to null (unless already changed)
            if ($commentsEvent->getUser() === $this) {
                $commentsEvent->setUser(null);
            }
        }

        return $this;
    }
}
