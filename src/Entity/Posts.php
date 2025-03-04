<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


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

    #[ORM\Column(type: 'json', nullable: true)]
    private array $likes = [];
    
    #[ORM\Column(type: 'json', nullable: true)]
    private array $dislikes = [];

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


public function getLikes(): array
{
    return $this->likes;
}

public function setLikes(array $likes): self
{
    $this->likes = $likes;
    return $this;
}

public function addLike(int $userId): self
{
    if (!in_array($userId, $this->likes)) {
        $this->likes[] = $userId;
        // Remove from dislikes if present
        $this->removeDislike($userId);
    }
    return $this;
}

public function removeLike(int $userId): self
{
    if (($key = array_search($userId, $this->likes)) !== false) {
        unset($this->likes[$key]);
        $this->likes = array_values($this->likes); // Reindex array
    }
    return $this;
}

public function getDislikes(): array
{
    return $this->dislikes;
}

public function setDislikes(array $dislikes): self
{
    $this->dislikes = $dislikes;
    return $this;
}

public function addDislike(int $userId): self
{
    if (!in_array($userId, $this->dislikes)) {
        $this->dislikes[] = $userId;
        // Remove from likes if present
        $this->removeLike($userId); // Correct method name
    }
    return $this;
}

public function removeDislike(int $userId): self
{
    if (($key = array_search($userId, $this->dislikes)) !== false) {
        unset($this->dislikes[$key]);
        $this->dislikes = array_values($this->dislikes); // Reindex array
    }
    return $this;
}

// Helper methods
public function getLikesCount(): int
{
    return count($this->likes);
}

public function getDislikesCount(): int
{
    return count($this->dislikes);
}

public function getUserReaction(?User $user): ?string
{
    if (!$user) {
        return null;
    }
    
    $userId = $user->getId();
    
    if (in_array($userId, $this->likes)) {
        return 'like';
    }
    
    if (in_array($userId, $this->dislikes)) {
        return 'dislike';
    }
    
    return null;
    if ($type === 'like') {
        // Débogage
        error_log('Adding like for user ' . $userId);
        try {
            $post->addLike($userId);
        } catch (\Exception $e) {
            error_log('Error adding like: ' . $e->getMessage());
            throw $e;
        }
    }
}
 }
