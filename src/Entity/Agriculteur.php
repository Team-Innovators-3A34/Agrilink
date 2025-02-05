<?php

namespace App\Entity;

use App\Repository\AgriculteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgriculteurRepository::class)]
class Agriculteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ressource = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRessource(): ?string
    {
        return $this->ressource;
    }

    public function setRessource(string $ressource): static
    {
        $this->ressource = $ressource;

        return $this;
    }
}
