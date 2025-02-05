<?php

namespace App\Entity;

use App\Repository\RessourceInvestorsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RessourceInvestorsRepository::class)]
class RessourceInvestors 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $RessourceAgricol = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRessourceAgricol(): ?string
    {
        return $this->RessourceAgricol;
    }

    public function setRessourceAgricol(string $RessourceAgricol): static
    {
        $this->RessourceAgricol = $RessourceAgricol;

        return $this;
    }
}
