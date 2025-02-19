<?php

namespace App\Entity;

use App\Repository\RessourcesRepository;
<<<<<<< HEAD
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
=======
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> origin/gestionressources

#[ORM\Entity(repositoryClass: RessourcesRepository::class)]
class Ressources
{
<<<<<<< HEAD
=======
   
 
>>>>>>> origin/gestionressources
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

<<<<<<< HEAD
    #[ORM\Column(length: 255)]
    private ?string $Type = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $Status = null;

    #[ORM\ManyToOne(inversedBy: 'ressources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $OwnerId = null;

    /**
     * @var Collection<int, Demandes>
     */
    #[ORM\ManyToMany(targetEntity: Demandes::class, mappedBy: 'RessourcesId')]
    private Collection $demandes;

    public function __construct()
    {
        $this->demandes = new ArrayCollection();
    }

=======
    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: "L'ID doit être un nombre positif.")]
    private ?int $owner_id_id = null;

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

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le non ne peut pas être vide.")]
    private ?string $image = null;

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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;
   
>>>>>>> origin/gestionressources
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

    public function getOwnerIdId(): ?int
    {
        return $this->owner_id_id;
    }

    public function setOwnerIdId(?int $owner_id_id): static
    {
        $this->owner_id_id = $owner_id_id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
>>>>>>> origin/gestionressources

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
>>>>>>> origin/gestionressources

        return $this;
    }

<<<<<<< HEAD
    public function getOwnerId(): ?user
    {
        return $this->OwnerId;
    }

    public function setOwnerId(?user $OwnerId): static
    {
        $this->OwnerId = $OwnerId;
=======
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
>>>>>>> origin/gestionressources

        return $this;
    }

<<<<<<< HEAD
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
            $demande->addRessourcesId($this);
        }
=======
    public function getNameR(): ?string
    {
        return $this->name_r;
    }

    public function setNameR(string $name_r): static
    {
        $this->name_r = $name_r;
>>>>>>> origin/gestionressources

        return $this;
    }

<<<<<<< HEAD
    public function removeDemande(Demandes $demande): static
    {
        if ($this->demandes->removeElement($demande)) {
            $demande->removeRessourcesId($this);
        }
=======
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
>>>>>>> origin/gestionressources

        return $this;
    }
}
