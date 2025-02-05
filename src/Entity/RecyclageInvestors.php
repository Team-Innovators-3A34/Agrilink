<?php

namespace App\Entity;

use App\Repository\RecyclageInvestorsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecyclageInvestorsRepository::class)]
class RecyclageInvestors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, RecyclagePoint>
     */
    #[ORM\OneToMany(targetEntity: RecyclagePoint::class, mappedBy: 'Owner')]
    private Collection $recyclagePoints;

    public function __construct()
    {
        $this->recyclagePoints = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $recyclagePoint->setOwner($this);
        }

        return $this;
    }

    public function removeRecyclagePoint(RecyclagePoint $recyclagePoint): static
    {
        if ($this->recyclagePoints->removeElement($recyclagePoint)) {
            // set the owning side to null (unless already changed)
            if ($recyclagePoint->getOwner() === $this) {
                $recyclagePoint->setOwner(null);
            }
        }

        return $this;
    }
}
