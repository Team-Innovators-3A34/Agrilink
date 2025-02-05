<?php

namespace App\Entity;

use App\Repository\RecycledProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecycledProduitRepository::class)]
class RecycledProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ProductName = null;

    #[ORM\Column(length: 255)]
    private ?string $Categorie = null;

    /**
     * @var Collection<int, RecyclageRapport>
     */
    #[ORM\ManyToMany(targetEntity: RecyclageRapport::class, mappedBy: 'Product')]
    private Collection $recyclageRapports;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $RecyclingMethod = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $MaterialType = null;

    public function __construct()
    {
        $this->recyclageRapports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->ProductName;
    }

    public function setProductName(string $ProductName): static
    {
        $this->ProductName = $ProductName;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->Categorie;
    }

    public function setCategorie(string $Categorie): static
    {
        $this->Categorie = $Categorie;

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
            $recyclageRapport->addProduct($this);
        }

        return $this;
    }

    public function removeRecyclageRapport(RecyclageRapport $recyclageRapport): static
    {
        if ($this->recyclageRapports->removeElement($recyclageRapport)) {
            $recyclageRapport->removeProduct($this);
        }

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getRecyclingMethod(): ?string
    {
        return $this->RecyclingMethod;
    }

    public function setRecyclingMethod(?string $RecyclingMethod): static
    {
        $this->RecyclingMethod = $RecyclingMethod;

        return $this;
    }

    public function getMaterialType(): ?string
    {
        return $this->MaterialType;
    }

    public function setMaterialType(?string $MaterialType): static
    {
        $this->MaterialType = $MaterialType;

        return $this;
    }
}
