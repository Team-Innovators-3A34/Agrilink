<?php

namespace App\Entity;

use App\Repository\DemandesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: DemandesRepository::class)]
class Demandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $demande_id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;


    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "  date d'expiration ne peut pas être vide.")]
    private ?\DateTimeInterface $expire_date = null;

    #[ORM\Column(length: 255)]
    private ?string $status =  "pending";

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "  message ne peut pas être vide.")]
    private ?string $message = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $propositon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reponse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomdemandeur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomowner = null;

    #[ORM\Column(length: 255)]
    private ?string $priorite = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $userId = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ressources $RessourceId = null;

    #[ORM\ManyToOne(inversedBy: 'todemandes')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $toUser = null;


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getExpireDate(): ?\DateTimeInterface
    {
        return $this->expire_date;
    }

    public function setExpireDate(\DateTimeInterface $expire_date): static
    {
        $this->expire_date = $expire_date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getDemandeId(): ?int
    {
        return $this->demande_id;
    }

    public function setDemandeId(int $demande_id): static
    {
        $this->demande_id = $demande_id;

        return $this;
    }

    public function getPropositon(): ?string
    {
        return $this->propositon;
    }

    public function setPropositon(?string $propositon): static
    {
        $this->propositon = $propositon;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getNomdemandeur(): ?string
    {
        return $this->nomdemandeur;
    }

    public function setNomdemandeur(?string $nomdemandeur): static
    {
        $this->nomdemandeur = $nomdemandeur;

        return $this;
    }

    public function getNomowner(): ?string
    {
        return $this->nomowner;
    }

    public function setNomowner(?string $nomowner): static
    {
        $this->nomowner = $nomowner;

        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(string $priorite): static
    {
        $this->priorite = $priorite;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getRessourceId(): ?Ressources
    {
        return $this->RessourceId;
    }

    public function setRessourceId(?Ressources $RessourceId): static
    {
        $this->RessourceId = $RessourceId;

        return $this;
    }

    public function getToUser(): ?User
    {
        return $this->toUser;
    }

    public function setToUser(?User $toUser): static
    {
        $this->toUser = $toUser;

        return $this;
    }
}
