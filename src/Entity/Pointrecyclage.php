<?php

namespace App\Entity;

use App\Repository\PointrecyclageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PointrecyclageRepository::class)]
class Pointrecyclage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le nom doit contenir au moins 3 caractères.")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 10, max: 500, minMessage: "La description doit contenir au moins 10 caractères.")]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La longitude est obligatoire.")]
    #[Assert\Regex(pattern: "/^-?\d+\.\d+$/", message: "Format de longitude invalide.")]
    private ?float $longitude = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La latitude est obligatoire.")]
    #[Assert\Regex(pattern: "/^-?\d+\.\d+$/", message: "Format de latitude invalide.")]
    private ?float $latitude = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'pointrecyclages')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $owner = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "L'adresse doit contenir au moins 5 caractères.")]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Produitrecyclage>
     */
    #[ORM\OneToMany(targetEntity: Produitrecyclage::class, mappedBy: 'pointderecyclage', cascade: ['remove'], orphanRemoval: true)]
    private Collection $produitrecyclages;

    public function __construct()
    {
        $this->produitrecyclages = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Produitrecyclage>
     */
    public function getProduitrecyclages(): Collection
    {
        return $this->produitrecyclages;
    }

    public function addProduitrecyclage(Produitrecyclage $produitrecyclage): static
    {
        if (!$this->produitrecyclages->contains($produitrecyclage)) {
            $this->produitrecyclages->add($produitrecyclage);
            $produitrecyclage->setPointderecyclage($this);
        }

        return $this;
    }

    public function removeProduitrecyclage(Produitrecyclage $produitrecyclage): static
    {
        if ($this->produitrecyclages->removeElement($produitrecyclage)) {
            // set the owning side to null (unless already changed)
            if ($produitrecyclage->getPointderecyclage() === $this) {
                $produitrecyclage->setPointderecyclage(null);
            }
        }

        return $this;
    }
}
