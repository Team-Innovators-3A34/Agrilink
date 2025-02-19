<?php

namespace App\Entity;

use App\Repository\UserRepository;
<<<<<<< HEAD
<<<<<<< HEAD
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
=======
>>>>>>> origin/gestionpost
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
<<<<<<< HEAD
=======
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
>>>>>>> origin/gestionuser
=======
>>>>>>> origin/gestionpost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

<<<<<<< HEAD
<<<<<<< HEAD
    #[ORM\Column]
    private ?int $userId = null;
=======
    #[ORM\Column]
    private ?int $user_id = null;
>>>>>>> origin/gestionpost

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

<<<<<<< HEAD
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
=======
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\Length(min: 8, minMessage: "The password must be at least {{ limit }} characters long.")]
    #[Assert\Regex(pattern: "/\d/", message: "The password must contain at least one digit.")]
    #[Assert\Regex(pattern: "/[A-Z]/", message: "The password must contain at least one uppercase letter.")]
>>>>>>> origin/gestionuser
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

<<<<<<< HEAD
    #[ORM\Column]
    private ?int $phone_number = null;

    #[ORM\Column(length: 255)]
    private ?string $bio = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $experience = null;

    /**
     * @var Collection<int, Complaints>
     */
    #[ORM\OneToMany(targetEntity: Complaints::class, mappedBy: 'UserId')]
    private Collection $complaints;

    /**
     * @var Collection<int, Demandes>
     */
    #[ORM\OneToMany(targetEntity: Demandes::class, mappedBy: 'UserId')]
    private Collection $demandes;

    /**
     * @var Collection<int, Posts>
     */
    #[ORM\OneToMany(targetEntity: Posts::class, mappedBy: 'UserId')]
    private Collection $posts;

    /**
     * @var Collection<int, Ressources>
     */
    #[ORM\OneToMany(targetEntity: Ressources::class, mappedBy: 'OwnerId')]
    private Collection $ressources;

    public function __construct()
    {
        $this->complaints = new ArrayCollection();
        $this->demandes = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->ressources = new ArrayCollection();
    }
=======
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The name is required.")]
    #[Assert\Regex(pattern: "/^[a-zA-ZÀ-ÿ\s'-]+$/u", message: "The name must contain only letters.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The first name is required.")]
    #[Assert\Regex(pattern: "/^[a-zA-ZÀ-ÿ\s'-]+$/u", message: "The first name must contain only letters.")]
    private ?string $prenom = null;

    #[ORM\Column(length: 8)]
    #[Assert\NotBlank(message: "The phone number is required.")]
    #[Assert\Regex(pattern: "/^\d{8}$/", message: "The phone number must contain exactly 8 digits.")]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $verificationCode = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $codeExpirationDate = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $accountVerification = null;
>>>>>>> origin/gestionuser

=======
>>>>>>> origin/gestionpost
    public function getId(): ?int
    {
        return $this->id;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;
=======
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
>>>>>>> origin/gestionuser
=======
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;
>>>>>>> origin/gestionpost

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }
<<<<<<< HEAD

<<<<<<< HEAD
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
=======
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;
>>>>>>> origin/gestionuser

        return $this;
    }

<<<<<<< HEAD
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
=======
    public function getResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTimeInterface $resetTokenExpiresAt): static
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;
>>>>>>> origin/gestionuser

        return $this;
    }

<<<<<<< HEAD
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
=======
    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?string $verificationCode): static
    {
        $this->verificationCode = $verificationCode;
>>>>>>> origin/gestionuser

        return $this;
    }

<<<<<<< HEAD
    public function getPhoneNumber(): ?int
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(int $phone_number): static
    {
        $this->phone_number = $phone_number;
=======
    public function getCodeExpirationDate(): ?\DateTimeInterface
    {
        return $this->codeExpirationDate;
    }

    public function setCodeExpirationDate(?\DateTimeInterface $codeExpirationDate): static
    {
        $this->codeExpirationDate = $codeExpirationDate;
>>>>>>> origin/gestionuser

        return $this;
    }

<<<<<<< HEAD
    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): static
    {
        $this->bio = $bio;
=======
    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
>>>>>>> origin/gestionuser

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

<<<<<<< HEAD
    public function setDescription(string $description): static
=======
    public function setDescription(?string $description): static
>>>>>>> origin/gestionuser
    {
        $this->description = $description;

        return $this;
    }

<<<<<<< HEAD
    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(string $experience): static
    {
        $this->experience = $experience;
=======
    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
>>>>>>> origin/gestionuser

        return $this;
    }

<<<<<<< HEAD
    /**
     * @return Collection<int, Complaints>
     */
    public function getComplaints(): Collection
    {
        return $this->complaints;
    }

    public function addComplaint(Complaints $complaint): static
    {
        if (!$this->complaints->contains($complaint)) {
            $this->complaints->add($complaint);
            $complaint->setUserId($this);
        }
=======
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
>>>>>>> origin/gestionuser

        return $this;
    }

<<<<<<< HEAD
    public function removeComplaint(Complaints $complaint): static
    {
        if ($this->complaints->removeElement($complaint)) {
            // set the owning side to null (unless already changed)
            if ($complaint->getUserId() === $this) {
                $complaint->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Demandes>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demandes $demande): static
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes->add($demande);
            $demande->setUserId($this);
        }

        return $this;
    }

    public function removeDemande(Demandes $demande): static
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getUserId() === $this) {
                $demande->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Posts>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUserId($this);
        }

        return $this;
    }

    public function removePost(Posts $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUserId() === $this) {
                $post->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ressources>
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessource(Ressources $ressource): static
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources->add($ressource);
            $ressource->setOwnerId($this);
        }

        return $this;
    }

    public function removeRessource(Ressources $ressource): static
    {
        if ($this->ressources->removeElement($ressource)) {
            // set the owning side to null (unless already changed)
            if ($ressource->getOwnerId() === $this) {
                $ressource->setOwnerId(null);
            }
        }
=======
    public function getAccountVerification(): ?string
    {
        return $this->accountVerification;
    }

    public function setAccountVerification(string $accountVerification): static
    {
        $this->accountVerification = $accountVerification;
>>>>>>> origin/gestionuser

        return $this;
    }
=======
>>>>>>> origin/gestionpost
}
