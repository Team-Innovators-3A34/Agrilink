<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'utilisateur ne peut pas être vide.")]
    #[Assert\Type(type: "string", message: "Le nom doit être une chaîne de caractères.")]
    private ?string $nom_user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email de l'utilisateur ne peut pas être vide.")]
    #[Assert\Email(message: "Veuillez entrer un email valide.")]
    private ?string $mail_user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide.")]
    #[Assert\Type(type: "string", message: "Le titre doit être une chaîne de caractères.")]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le contenu de la réclamation ne peut pas être vide.")]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut ne peut pas être vide.")]
    #[Assert\Choice(choices: ["En cours", "Terminé", "Rejeté"], message: "Le statut doit être 'En cours', 'Terminé' ou 'Rejeté'.")]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\Type(type: "\DateTimeInterface", message: "La date doit être valide.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: TypeRec::class)]
    #[ORM\JoinColumn(name: "type_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Assert\NotNull(message: "Veuillez sélectionner un type.")]
    private ?TypeRec $type = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\Type(type: "integer", message: "La priorité doit être un entier.")]
    private ?int $priorite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'id_reclamation', targetEntity: Reponses::class, orphanRemoval: true)]
    private Collection $reponses;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    #[Assert\NotNull(message: "Veuillez associer un utilisateur.")]
    private ?User $id_user = null;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomUser(): ?string
    {
        return $this->nom_user;
    }

    public function setNomUser(string $nom_user): static
    {
        $this->nom_user = $nom_user;
        return $this;
    }

    public function getMailUser(): ?string
    {
        return $this->mail_user;
    }

    public function setMailUser(string $mail_user): static
    {
        $this->mail_user = $mail_user;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getType(): ?TypeRec
    {
        return $this->type;
    }

    public function setType(?TypeRec $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getPriorite(): ?int
    {
        return $this->priorite;
    }

    public function setPriorite(?int $priorite): static
    {
        $this->priorite = $priorite;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): static
    {
        $this->id_user = $id_user;
        return $this;
    }
}
