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

    #[ORM\Column(nullable: true)]
    private ?int $id_user = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message="Le nom de l'utilisateur ne peut pas être vide.")
     * @Assert\Type("string", message="Le nom doit être une chaîne de caractères.")
     */
    private ?string $nom_user = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message="L'email de l'utilisateur ne peut pas être vide.")
     * @Assert\Email(message="Veuillez entrer un email valide.")
     */
    private ?string $mail_user = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message="Le titre ne peut pas être vide.")
     * @Assert\Type("string", message="Le titre doit être une chaîne de caractères.")
     */
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message="Le contenu de la réclamation ne peut pas être vide.")
     * @Assert\Type("string", message="Le contenu doit être une chaîne de caractères.")
     */
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message="Le statut ne peut pas être vide.")
     * @Assert\Choice(choices={"En cours", "Terminé", "Rejeté"}, message="Le statut doit être 'En cours', 'Terminé' ou 'Rejeté'.")
     */
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    /**
     * @Assert\NotBlank(message="La date ne peut pas être vide.")
     * @Assert\Type("\DateTime", message="La date doit être valide.")
     */
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    /**
     * @Assert\Type("string", message="Le type doit être une chaîne de caractères.")
     */
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Assert\NotBlank(message="La priorité ne peut pas être vide.")
     * @Assert\Type("integer", message="La priorité doit être un entier.")
     */
    private ?int $priorite = null;

    #[ORM\Column(length: 255, nullable: true)]
    /**
     * @Assert\Image(
     *     maxSize="5M",
     *     mimeTypes={"image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage="Veuillez télécharger une image valide (jpeg, png, gif)."
     * )
     */
    private ?string $image = null;

    /**
     * @var Collection<int, Reponses>
     */
    #[ORM\OneToMany(targetEntity: Reponses::class, mappedBy: 'id_reclamation', orphanRemoval: true)]
    private Collection $reponses;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etatRec = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etatuser = null;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(?int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
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

    /**
     * @return Collection<int, Reponses>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponses $reponse): static
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setIdReclamation($this);
        }

        return $this;
    }

    public function removeReponse(Reponses $reponse): static
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getIdReclamation() === $this) {
                $reponse->setIdReclamation(null);
            }
        }

        return $this;
    }

    public function getEtatRec(): ?string
    {
        return $this->etatRec;
    }

    public function setEtatRec(?string $etatRec): static
    {
        $this->etatRec = $etatRec;

        return $this;
    }

    public function getEtatuser(): ?string
    {
        return $this->etatuser;
    }

    public function setEtatuser(?string $etatuser): static
    {
        $this->etatuser = $etatuser;

        return $this;
    }
}
