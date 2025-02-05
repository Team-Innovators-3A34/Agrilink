<?php

namespace App\Entity;

use App\Repository\InvestorsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvestorsRepository::class)]
class Investors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $CapitalDispo = null;

    #[ORM\Column(length: 255)]
    private ?string $Domaine = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCapitalDispo(): ?float
    {
        return $this->CapitalDispo;
    }

    public function setCapitalDispo(float $CapitalDispo): static
    {
        $this->CapitalDispo = $CapitalDispo;

        return $this;
    }

    public function getDomaine(): ?string
    {
        return $this->Domaine;
    }

    public function setDomaine(string $Domaine): static
    {
        $this->Domaine = $Domaine;

        return $this;
    }
}
