<?php

namespace App\Entity;

use App\Repository\ProduitrecyclageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitrecyclageRepository::class)]
class Produitrecyclage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le nom doit contenir au moins 3 caractères.")]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'produitrecyclages')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Assert\NotNull(message: "Le point de recyclage est obligatoire.")]
    private ?Pointrecyclage $pointderecyclage = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $recycledAt = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La quantité est obligatoire.")]
    #[Assert\Positive(message: "La quantité doit être un nombre positif.")]
    private ?int $quantite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 10, max: 255, minMessage: "La description doit contenir au moins 10 caractères.")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'produitrecyclages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

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

    public function getPointderecyclage(): ?Pointrecyclage
    {
        return $this->pointderecyclage;
    }

    public function setPointderecyclage(?Pointrecyclage $pointderecyclage): static
    {
        $this->pointderecyclage = $pointderecyclage;

        return $this;
    }

    public function getRecycledAt(): ?\DateTimeImmutable
    {
        return $this->recycledAt;
    }

    public function setRecycledAt(\DateTimeImmutable $recycledAt): static
    {
        $this->recycledAt = $recycledAt;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

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
}
