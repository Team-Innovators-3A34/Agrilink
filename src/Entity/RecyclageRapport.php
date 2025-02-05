<?php

namespace App\Entity;

use App\Repository\RecyclageRapportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecyclageRapportRepository::class)]
class RecyclageRapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column]
    private ?int $Quantity = null;

    /**
     * @var Collection<int, RecycledProduit>
     */
    #[ORM\ManyToMany(targetEntity: RecycledProduit::class, inversedBy: 'recyclageRapports')]
    private Collection $Product;

    #[ORM\ManyToOne(inversedBy: 'recyclageRapports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RecyclagePoint $RecyclingPoint = null;

    public function __construct()
    {
        $this->Product = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): static
    {
        $this->Date = $Date;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->Quantity;
    }

    public function setQuantity(int $Quantity): static
    {
        $this->Quantity = $Quantity;

        return $this;
    }

    /**
     * @return Collection<int, RecycledProduit>
     */
    public function getProduct(): Collection
    {
        return $this->Product;
    }

    public function addProduct(RecycledProduit $product): static
    {
        if (!$this->Product->contains($product)) {
            $this->Product->add($product);
        }

        return $this;
    }

    public function removeProduct(RecycledProduit $product): static
    {
        $this->Product->removeElement($product);

        return $this;
    }

    public function getRecyclingPoint(): ?RecyclagePoint
    {
        return $this->RecyclingPoint;
    }

    public function setRecyclingPoint(?RecyclagePoint $RecyclingPoint): static
    {
        $this->RecyclingPoint = $RecyclingPoint;

        return $this;
    }
}
