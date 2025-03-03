<?php

namespace App\Entity;

use App\Repository\RatingRessourceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatingRessourceRepository::class)]
class RatingRessource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ratingRessources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ressources $ressource = null;

    #[ORM\ManyToOne(inversedBy: 'ratingRessources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $rate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $rated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRessource(): ?Ressources
    {
        return $this->ressource;
    }

    public function setRessource(?Ressources $ressource): static
    {
        $this->ressource = $ressource;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getRatedAt(): ?\DateTimeImmutable
    {
        return $this->rated_at;
    }

    public function setRatedAt(\DateTimeImmutable $rated_at): static
    {
        $this->rated_at = $rated_at;

        return $this;
    }
}
