<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\OpenAIService;


#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Title cannot be empty')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Title must be at least {{ limit }} characters long',
        maxMessage: 'Title cannot be longer than {{ limit }} characters'
    )]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private string $description = '';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user_id = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $images = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $sentiment = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $sentimentScore = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $aiGeneratedTip = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Reaction::class, orphanRemoval: true)]
    private Collection $reactions;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
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
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): static
    {
        $this->images = $images;

        return $this;
    }


    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    /**
     * Get reactions of a specific type
     */
    public function getReactionsByType(string $type): array
    {
        return $this->reactions->filter(
            fn(Reaction $reaction) => $reaction->getType() === $type
        )->toArray();
    }

    /**
     * Count reactions of a specific type
     */
    public function getReactionCountByType(string $type): int
    {
        return count($this->getReactionsByType($type));
    }

    /**
     * Check if a user has reacted with a specific type
     */
    public function hasUserReaction(User $user, ?string $type = null): bool
    {
        foreach ($this->reactions as $reaction) {
            if ($reaction->getUser() === $user) {
                if ($type === null || $reaction->getType() === $type) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get the reaction type for a specific user
     */
    public function getUserReactionType(User $user): ?string
    {
        foreach ($this->reactions as $reaction) {
            if ($reaction->getUser() === $user) {
                return $reaction->getType();
            }
        }
        return null;
    }

    public function addReaction(Reaction $reaction): self
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions->add($reaction);
            $reaction->setPost($this);
        }

        return $this;
    }

    public function removeReaction(Reaction $reaction): self
    {
        if ($this->reactions->removeElement($reaction)) {
            // set the owning side to null (unless already changed)
            if ($reaction->getPost() === $this) {
                $reaction->setPost(null);
            }
        }

        return $this;
    }

    // Getter et Setter pour sentiment
    public function getSentiment(): ?string
    {
        return $this->sentiment;
    }

    public function setSentiment(?string $sentiment): self
    {
        $this->sentiment = $sentiment;
        return $this;
    }

    // Getter et Setter pour sentimentScore
    public function getSentimentScore(): ?float
    {
        return $this->sentimentScore;
    }

    public function setSentimentScore(?float $sentimentScore): self
    {
        $this->sentimentScore = $sentimentScore;
        return $this;
    }


    public function getAiGeneratedTip(): ?string
    {
        return $this->aiGeneratedTip;
    }

    public function setAiGeneratedTip(?string $aiGeneratedTip): self
    {
        $this->aiGeneratedTip = $aiGeneratedTip;

        return $this;
    }
}
