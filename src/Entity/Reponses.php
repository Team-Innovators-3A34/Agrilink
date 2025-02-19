<?php

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

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message="Le contenu de la réponse ne peut pas être vide.")
     * @Assert\Type("string", message="Le contenu doit être une chaîne de caractères.")
     */
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    /**
     * @Assert\DateTime(message="Veuillez entrer une date et heure valides.")
     * @Assert\NotBlank(message="La date de la réponse ne peut pas être vide.")
     */
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Assert\Type("bool", message="La valeur doit être un booléen.")
     */
    private ?bool $isAuto = null;


    // Cette propriété ne devrait plus être utilisée
    // #[ORM\Column]
    // private ?int $id_Reclamation = null;

    // La relation ManyToOne pour Reclamation
    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reclamation $id_reclamation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function isAuto(): ?bool
    {
        return $this->isAuto;
    }

    public function setIsAuto(?bool $isAuto): static
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
}
