<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
<<<<<<< HEAD
use Doctrine\ORM\Mapping as ORM;
=======
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

>>>>>>> origin/gestionpost

#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

<<<<<<< HEAD
    #[ORM\Column(length: 255)]
    private ?string $Type = null;

    #[ORM\Column(length: 255)]
    private ?string $Title = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $Status = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $UserId = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'PostId')]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }
=======
    #[ORM\Column]
    private ?int $user_id_id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

   /*  #[Assert\NotBlank(message: "Title cannot be empty")]
    #[Assert\Length(
    min: 3,
    max: 255,
    minMessage: "Title must be at least {{ limit }} characters long",
    maxMessage: "Title cannot be longer than {{ limit }} characters"
    )] */
    #[ORM\Column(length: 255)]
    private string $title = '';

  
    /* #[Assert\NotBlank(message: 'Description cannot be empty')]
    #[Assert\Length(
    min: 3,
    max: 255,
    minMessage: 'Description must be at least {{ limit }} characters long',
    maxMessage: 'Description cannot be longer than {{ limit }} characters'
)] */
    #[ORM\Column(length: 255)]
    private string $description = '';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;


    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $file;


    
    #[Assert\File(
        maxSize: "40M",
        mimeTypes: [
            "image/jpeg",
            "image/png",
            "image/gif",
            "application/pdf",
            "video/mp4"
        ],
        mimeTypesMessage: "Please upload a valid file (JPG, PNG, GIF, PDF, MP4)"
    )]
    private ?File $fileUpload = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class)]
    private Collection $comments;


    /* #[ORM\JoinColumn(name: 'user_id_id', referencedColumnName: 'id')] */
    /* private ?User $user = null; */
>>>>>>> origin/gestionpost

    public function getId(): ?int
    {
        return $this->id;
    }

<<<<<<< HEAD
    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): static
    {
        $this->Type = $Type;
=======
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUserIdId(): ?int
    {
        return $this->user_id_id;
    }

    public function setUserIdId(int $user_id_id): static
    {
        $this->user_id_id = $user_id_id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
>>>>>>> origin/gestionpost

        return $this;
    }

    public function getTitle(): ?string
    {
<<<<<<< HEAD
        return $this->Title;
    }

    public function setTitle(string $Title): static
    {
        $this->Title = $Title;
=======
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
>>>>>>> origin/gestionpost

        return $this;
    }

    public function getDescription(): ?string
    {
<<<<<<< HEAD
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;
=======
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
>>>>>>> origin/gestionpost

        return $this;
    }

<<<<<<< HEAD
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;
=======
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;
>>>>>>> origin/gestionpost

        return $this;
    }

    public function getStatus(): ?string
    {
<<<<<<< HEAD
        return $this->Status;
    }

    public function setStatus(string $Status): static
    {
        $this->Status = $Status;
=======
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
>>>>>>> origin/gestionpost

        return $this;
    }

<<<<<<< HEAD
    public function getUserId(): ?user
    {
        return $this->UserId;
    }

    public function setUserId(?user $UserId): static
    {
        $this->UserId = $UserId;

        return $this;
    }

=======
    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getFileUpload(): ?File
    {
        return $this->fileUpload;
    }

    public function setFileUpload(?File $fileUpload): self
    {
        $this->fileUpload = $fileUpload;
        return $this;
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

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

>>>>>>> origin/gestionpost
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
<<<<<<< HEAD
            $comment->setPostId($this);
=======
            $comment->setPost($this);
>>>>>>> origin/gestionpost
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
<<<<<<< HEAD
            if ($comment->getPostId() === $this) {
                $comment->setPostId(null);
=======
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
>>>>>>> origin/gestionpost
            }
        }

        return $this;
    }
<<<<<<< HEAD
=======

>>>>>>> origin/gestionpost
}
