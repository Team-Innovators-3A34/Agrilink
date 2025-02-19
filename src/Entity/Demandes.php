<?php

namespace App\Entity;

use App\Repository\DemandesRepository;
<<<<<<< HEAD
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
=======
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotNull;
>>>>>>> origin/gestionressources

#[ORM\Entity(repositoryClass: DemandesRepository::class)]
class Demandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
<<<<<<< HEAD
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
=======
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
>>>>>>> origin/gestionressources

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
<<<<<<< HEAD
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;
=======
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;
>>>>>>> origin/gestionressources

        return $this;
    }

    public function getExpireDate(): ?\DateTimeInterface
    {
<<<<<<< HEAD
        return $this->ExpireDate;
    }

    public function setExpireDate(\DateTimeInterface $ExpireDate): static
    {
        $this->ExpireDate = $ExpireDate;
=======
        return $this->expire_date;
    }

    public function setExpireDate(\DateTimeInterface $expire_date): static
    {
        $this->expire_date = $expire_date;
>>>>>>> origin/gestionressources

        return $this;
    }

    public function getStatus(): ?string
    {
<<<<<<< HEAD
        return $this->Status;
    }

    public function setStatus(string $Status): static
    {
        $this->Status = $Status;
=======
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
>>>>>>> origin/gestionressources

        return $this;
    }

    public function getMessage(): ?string
    {
<<<<<<< HEAD
        return $this->Message;
    }

    public function setMessage(string $Message): static
    {
        $this->Message = $Message;
=======
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
>>>>>>> origin/gestionressources

        return $this;
    }

<<<<<<< HEAD
    public function getUserId(): ?user
    {
        return $this->UserId;
    }

    public function setUserId(?user $UserId): static
    {
        $this->UserId = $UserId;
=======
    public function getDemandeId(): ?int
    {
        return $this->demande_id;
    }

    public function setDemandeId(int $demande_id): static
    {
        $this->demande_id = $demande_id;
>>>>>>> origin/gestionressources

        return $this;
    }

<<<<<<< HEAD
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
=======
    public function getPropositon(): ?string
    {
        return $this->propositon;
    }

    public function setPropositon(?string $propositon): static
    {
        $this->propositon = $propositon;
>>>>>>> origin/gestionressources

        return $this;
    }

<<<<<<< HEAD
    public function removeRessourcesId(ressources $ressourcesId): static
    {
        $this->RessourcesId->removeElement($ressourcesId);
=======
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
>>>>>>> origin/gestionressources

        return $this;
    }
}
