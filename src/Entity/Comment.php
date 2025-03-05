<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $post_id_id = null;


    #[ORM\ManyToOne(targetEntity: Posts::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'post_id_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Posts $post = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le contenu est obligatoire.")]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_commented = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $is_toxic = false;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $toxicity_score = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPostIdId(): ?int
    {
        return $this->post_id_id;
    }

    public function setPostIdId(int $post_id_id): static
    {
        $this->post_id_id = $post_id_id;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPost(): ?Posts
    {
        return $this->post;
    }

    public function setPost(?Posts $post): static
    {
        $this->post = $post;
        if ($post) {
            $this->post_id_id = $post->getId();
        }
        return $this;
    }

    public function getUserCommented(): ?User
    {
        return $this->user_commented;
    }

    public function setUserCommented(?User $user_commented): static
    {
        $this->user_commented = $user_commented;

        return $this;
    }

    public function getIsToxic(): bool
    {
        return $this->is_toxic;
    }

    public function setIsToxic(bool $is_toxic): static
    {
        $this->is_toxic = $is_toxic;
        return $this;
    }

    public function getToxicityScore(): ?float
    {
        return $this->toxicity_score;
    }

    public function setToxicityScore(?float $toxicity_score): static
    {
        $this->toxicity_score = $toxicity_score;
        return $this;
    }
}
