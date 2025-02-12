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
    #[ORM\ManyToOne(targetEntity: Ressources::class)]
    #[ORM\JoinColumn(name: "idR", referencedColumnName: "id", nullable: false)]
    private ?int $idR = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "  user_id_id ne peut pas être vide.")]
    private ?int $user_id_id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
   
    private ?\DateTimeInterface $expire_date = null;

    #[ORM\Column(length: 255)]
    private ?string $status =  "pending";

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "  user_id_id ne peut pas être vide.")]

    private ?string $message = null;

   

    public function getIdR(): ?int
    {
        return $this->idR;
    }

    public function setIdR(int $idR): static
    {
        $this->idR = $idR;

        return $this;
    }

    public function getUserIdId(): ?int
    {
        return $this->user_id_id;
    }

    public function setUserIdId(int $user_id_id): static
    {
        $this->user_id_id = $user_id_id;

        return $this;
    }

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
}
