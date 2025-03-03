<?php

namespace App\Entity;

use App\Repository\RessourcesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RessourcesRepository::class)]
class Ressources
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le type ne peut pas être vide.")]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(
        min: 20,
        minMessage: "Votre description doit contenir au minimum 20 caractères."
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut ne peut pas être vide.")]
    private ?string $status = null;



    #[ORM\Column(length: 255)]
    private ?string $name_r = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $marque = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $prix_location = null;

    #[ORM\Column(nullable: true)]
    private ?float $superficie = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $images = [];

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "L'adresse ne peut pas être vide.")]
    private ?string $adresse = null;

    #[ORM\ManyToOne(inversedBy: 'ressources')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $userId = null;

    /**
     * @var Collection<int, Demandes>
     */
    #[ORM\OneToMany(targetEntity: Demandes::class, mappedBy: 'RessourceId')]
    private Collection $demandes;

    /**
     * @var Collection<int, RatingRessource>
     */
    #[ORM\OneToMany(targetEntity: RatingRessource::class, mappedBy: 'ressource', orphanRemoval: true)]
    private Collection $ratingRessources;

    public function __construct()
    {
        $this->demandes = new ArrayCollection();
        $this->ratingRessources = new ArrayCollection();
    }

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

    public function setType(?string $type): static
    {
        $this->type = $type;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }



    public function getNameR(): ?string
    {
        return $this->name_r;
    }

    public function setNameR(string $name_r): static
    {
        $this->name_r = $name_r;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(?string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getPrixLocation(): ?string
    {
        return $this->prix_location;
    }

    public function setPrixLocation(?string $prix_location): static
    {
        $this->prix_location = $prix_location;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(?float $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return Collection<int, Demandes>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demandes $demande): static
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes->add($demande);
            $demande->setRessourceId($this);
        }

        return $this;
    }

    public function removeDemande(Demandes $demande): static
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getRessourceId() === $this) {
                $demande->setRessourceId(null);
            }
        }

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

    public function addImage(string $image): static
    {
        $this->images[] = $image;

        return $this;
    }

    public function removeImage(string $image): static
    {
        $key = array_search($image, $this->images, true);

        if ($key !== false) {
            unset($this->images[$key]);
        }

        return $this;
    }

    /**
     * @return Collection<int, RatingRessource>
     */
    public function getRatingRessources(): Collection
    {
        return $this->ratingRessources;
    }

    public function addRatingRessource(RatingRessource $ratingRessource): static
    {
        if (!$this->ratingRessources->contains($ratingRessource)) {
            $this->ratingRessources->add($ratingRessource);
            $ratingRessource->setRessource($this);
        }

        return $this;
    }

    public function removeRatingRessource(RatingRessource $ratingRessource): static
    {
        if ($this->ratingRessources->removeElement($ratingRessource)) {
            // set the owning side to null (unless already changed)
            if ($ratingRessource->getRessource() === $this) {
                $ratingRessource->setRessource(null);
            }
        }

        return $this;
    }
}
