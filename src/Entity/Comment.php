<?php

namespace App\Entity;

use App\Repository\CommentRepository;
<<<<<<< HEAD
use Doctrine\ORM\Mapping as ORM;
=======
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> origin/gestionpost

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

<<<<<<< HEAD
    #[ORM\Column(length: 255)]
    private ?string $Content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Posts $PostId = null;
=======
  #[ORM\Column]
    private ?int $post_id_id = null;
 

 #[ORM\ManyToOne(targetEntity: Posts::class, inversedBy: 'comments')]
 #[ORM\JoinColumn(name: 'post_id_id', referencedColumnName: 'id')]
 private ?Posts $post = null;


    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;
>>>>>>> origin/gestionpost

    public function getId(): ?int
    {
        return $this->id;
    }

<<<<<<< HEAD
    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(string $Content): static
    {
        $this->Content = $Content;
=======
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

<<<<<<< HEAD
    public function getPostId(): ?Posts
    {
        return $this->PostId;
    }

    public function setPostId(?Posts $PostId): static
    {
        $this->PostId = $PostId;

        return $this;
    }
=======
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
>>>>>>> origin/gestionpost
}
