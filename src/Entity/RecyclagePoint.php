<?php

namespace App\Entity;

use App\Repository\RecyclagePointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecyclagePointRepository::class)]
class RecyclagePoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column]
    private ?int $MaxCapacite = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?float $Longitude = null;

    #[ORM\Column]
    private ?float $Latitude = null;

    #[ORM\ManyToOne(inversedBy: 'recyclagePoints')]
    private ?RecyclageInvestors $Owner = null;

    /**
     * @var Collection<int, RecyclageRapport>
     */
    #[ORM\OneToMany(targetEntity: RecyclageRapport::class, mappedBy: 'RecyclingPoint')]
    private Collection $recyclageRapports;

   

    #[ORM\ManyToOne(inversedBy: 'recyclagePoints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $Types = null;

    public function __construct()
    {
        $this->recyclageRapports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getMaxCapacite(): ?int
    {
        return $this->MaxCapacite;
    }

    public function setMaxCapacite(int $MaxCapacite): static
    {
        $this->MaxCapacite = $MaxCapacite;

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

    public function getLongitude(): ?float
    {
        return $this->Longitude;
    }

    public function setLongitude(float $Longitude): static
    {
        $this->Longitude = $Longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->Latitude;
    }

    public function setLatitude(float $Latitude): static
    {
        $this->Latitude = $Latitude;

        return $this;
    }

    public function getOwner(): ?RecyclageInvestors
    {
        return $this->Owner;
    }

    public function setOwner(?RecyclageInvestors $Owner): static
    {
        $this->Owner = $Owner;

        return $this;
    }

    /**
     * @return Collection<int, RecyclageRapport>
     */
    public function getRecyclageRapports(): Collection
    {
        return $this->recyclageRapports;
    }

    public function addRecyclageRapport(RecyclageRapport $recyclageRapport): static
    {
        if (!$this->recyclageRapports->contains($recyclageRapport)) {
            $this->recyclageRapports->add($recyclageRapport);
            $recyclageRapport->setRecyclingPoint($this);
        }

        return $this;
    }

    public function removeRecyclageRapport(RecyclageRapport $recyclageRapport): static
    {
        if ($this->recyclageRapports->removeElement($recyclageRapport)) {
            // set the owning side to null (unless already changed)
            if ($recyclageRapport->getRecyclingPoint() === $this) {
                $recyclageRapport->setRecyclingPoint(null);
            }
        }

        return $this;
    }

   


    public function getTypes(): ?Type
    {
        return $this->Types;
    }

    public function setTypes(?Type $Types): static
    {
        $this->Types = $Types;

        return $this;
    }
}
