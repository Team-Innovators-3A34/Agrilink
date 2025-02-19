<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
=======
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> origin/gestionevent

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
<<<<<<< HEAD
    private ?string $Name = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    /**
     * @var Collection<int, Events>
     */
    #[ORM\OneToMany(targetEntity: Events::class, mappedBy: 'Categorie')]
=======
    #[Assert\NotBlank(message: "Le nom du catégorie est obligatoire.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le nom doit contenir au moins 3 caractères.")]
    private ?string $nom = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'categorie')]
>>>>>>> origin/gestionevent
    private Collection $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

<<<<<<< HEAD
    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;
=======
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
>>>>>>> origin/gestionevent

        return $this;
    }

    /**
<<<<<<< HEAD
     * @return Collection<int, Events>
=======
     * @return Collection<int, Event>
>>>>>>> origin/gestionevent
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

<<<<<<< HEAD
    public function addEvent(Events $event): static
=======
    public function addEvent(Event $event): static
>>>>>>> origin/gestionevent
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setCategorie($this);
        }

        return $this;
    }

<<<<<<< HEAD
    public function removeEvent(Events $event): static
=======
    public function removeEvent(Event $event): static
>>>>>>> origin/gestionevent
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCategorie() === $this) {
                $event->setCategorie(null);
            }
        }

        return $this;
    }
}
