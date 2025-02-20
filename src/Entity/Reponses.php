<?php
// src/Entity/Reponses.php

namespace App\Entity;

use App\Repository\ReponsesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponsesRepository::class)]
class Reponses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // La contrainte NotBlank s'appliquera uniquement dans le groupe "manual"
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le contenu ne peut pas être vide.", groups: ["manual"])]
    #[Assert\Type("string", message: "Le contenu doit être une chaîne de caractères.")]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "La date de la réponse ne peut pas être vide.", groups: ["manual"])]
    // Remplacer Assert\DateTime par Assert\Type pour valider que c'est bien un objet DateTime
    #[Assert\Type(type: "\DateTimeInterface", message: "Veuillez entrer une date et heure valides.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type("bool", message: "La valeur doit être un booléen.")]
    private ?bool $isAuto = null;

    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reclamation $id_reclamation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "La solution ne peut pas être vide.", groups: ["manual"])]
    private ?string $solution = null;

    // ---------- GETTERS & SETTERS ----------

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function getContent(): ?string
    {
        return $this->content;
    }
    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }
    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }
    public function isAuto(): ?bool
    {
        return $this->isAuto;
    }
    public function setIsAuto(?bool $isAuto): self
    {
        $this->isAuto = $isAuto;
        return $this;
    }
    public function getReclamation(): ?Reclamation
    {
        return $this->id_reclamation;
    }
    public function setReclamation(Reclamation $reclamation): self
    {
        $this->id_reclamation = $reclamation;
        return $this;
    }
    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }
    public function getSolution(): ?string
    {
        return $this->solution;
    }
    public function setSolution(?string $solution): self
    {
        $this->solution = $solution;
        return $this;
    }
}
