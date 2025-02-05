<?php

namespace App\Entity;

use App\Repository\DemandesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandesRepository::class)]
class Demandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $DemandeId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $ExpireDate = null;

    #[ORM\Column(length: 255)]
    private ?string $Status = null;

    #[ORM\Column(length: 255)]
    private ?string $Message = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $UserId = null;

    /**
     * @var Collection<int, ressources>
     */
    #[ORM\ManyToMany(targetEntity: ressources::class, inversedBy: 'demandes')]
    private Collection $RessourcesId;

    public function __construct()
    {
        $this->RessourcesId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDemandeId(): ?int
    {
        return $this->DemandeId;
    }

    public function setDemandeId(int $DemandeId): static
    {
        $this->DemandeId = $DemandeId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getExpireDate(): ?\DateTimeInterface
    {
        return $this->ExpireDate;
    }

    public function setExpireDate(\DateTimeInterface $ExpireDate): static
    {
        $this->ExpireDate = $ExpireDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->Status;
    }

    public function setStatus(string $Status): static
    {
        $this->Status = $Status;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->Message;
    }

    public function setMessage(string $Message): static
    {
        $this->Message = $Message;

        return $this;
    }

    public function getUserId(): ?user
    {
        return $this->UserId;
    }

    public function setUserId(?user $UserId): static
    {
        $this->UserId = $UserId;

        return $this;
    }

    /**
     * @return Collection<int, ressources>
     */
    public function getRessourcesId(): Collection
    {
        return $this->RessourcesId;
    }

    public function addRessourcesId(ressources $ressourcesId): static
    {
        if (!$this->RessourcesId->contains($ressourcesId)) {
            $this->RessourcesId->add($ressourcesId);
        }

        return $this;
    }

    public function removeRessourcesId(ressources $ressourcesId): static
    {
        $this->RessourcesId->removeElement($ressourcesId);

        return $this;
    }
}
