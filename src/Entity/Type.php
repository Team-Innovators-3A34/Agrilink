<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, RecyclagePoint>
     */
    #[ORM\OneToMany(targetEntity: RecyclagePoint::class, mappedBy: 'Types')]
    private Collection $recyclagePoints;

    public function __construct()
    {
        $this->recyclagePoints = new ArrayCollection();
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

    /**
     * @return Collection<int, RecyclagePoint>
     */
    public function getRecyclagePoints(): Collection
    {
        return $this->recyclagePoints;
    }

    public function addRecyclagePoint(RecyclagePoint $recyclagePoint): static
    {
        if (!$this->recyclagePoints->contains($recyclagePoint)) {
            $this->recyclagePoints->add($recyclagePoint);
            $recyclagePoint->setTypes($this);
        }

        return $this;
    }

    public function removeRecyclagePoint(RecyclagePoint $recyclagePoint): static
    {
        if ($this->recyclagePoints->removeElement($recyclagePoint)) {
            // set the owning side to null (unless already changed)
            if ($recyclagePoint->getTypes() === $this) {
                $recyclagePoint->setTypes(null);
            }
        }

        return $this;
    }
}
