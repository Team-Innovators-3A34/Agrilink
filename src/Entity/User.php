<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The adresse is required.")]
    private ?string $adresse = null;

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

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'userNotif', cascade: ['remove'], orphanRemoval: true)]
    private Collection $notifications;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'toUserNotif', cascade: ['remove'], orphanRemoval: true)]
    private Collection $toNotifications;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participants', cascade: ['remove'], orphanRemoval: true)]
    private Collection $events;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $latitude = null;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'personnes', cascade: ['remove'], orphanRemoval: true)]
    private Collection $conversations;

    /**
     * @var Collection<int, Messages>
     */
    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'sender', cascade: ['remove'], orphanRemoval: true)]
    private Collection $messages;

    #[ORM\Column(nullable: true)]
    private ?int $failedLoginAttempts = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lockUntil = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private ?bool $is2FA = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code2FA = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $code2FAexpiry = null;

    /**
     * @var Collection<int, Ressources>
     */
    #[ORM\OneToMany(targetEntity: Ressources::class, mappedBy: 'userId', cascade: ['remove'], orphanRemoval: true)]
    private Collection $ressources;

    /**
     * @var Collection<int, Demandes>
     */
    #[ORM\OneToMany(targetEntity: Demandes::class, mappedBy: 'userId', cascade: ['remove'], orphanRemoval: true)]
    private Collection $demandes;

    /**
     * @var Collection<int, Reclamation>
     */
    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'id_user', cascade: ['remove'], orphanRemoval: true)]
    private Collection $reclamations;

    /**
     * @var Collection<int, Demandes>
     */
    #[ORM\OneToMany(targetEntity: Demandes::class, mappedBy: 'toUser', cascade: ['remove'], orphanRemoval: true)]
    private Collection $todemandes;

    /**
     * @var Collection<int, Posts>
     */
    #[ORM\OneToMany(targetEntity: Posts::class, mappedBy: 'user_id', cascade: ['remove'], orphanRemoval: true)]
    private Collection $posts;

    /**
     * @var Collection<int, Pointrecyclage>
     */
    #[ORM\OneToMany(targetEntity: Pointrecyclage::class, mappedBy: 'owner', cascade: ['remove'], orphanRemoval: true)]
    private Collection $pointrecyclages;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user_commented', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Produitrecyclage>
     */
    #[ORM\OneToMany(targetEntity: Produitrecyclage::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $produitrecyclages;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    /**
     * @var Collection<int, Friendship>
     */
    #[ORM\OneToMany(targetEntity: Friendship::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $friendships;

    /**
     * @var Collection<int, Friendship>
     */
    #[ORM\OneToMany(targetEntity: Friendship::class, mappedBy: 'friend', orphanRemoval: true)]
    private Collection $myfriendships;

    /**
     * @var Collection<int, RatingRessource>
     */
    #[ORM\OneToMany(targetEntity: RatingRessource::class, mappedBy: 'user')]
    private Collection $ratingRessources;



    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->toNotifications = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->ressources = new ArrayCollection();
        $this->demandes = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->todemandes = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->pointrecyclages = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->produitrecyclages = new ArrayCollection();
        $this->friendships = new ArrayCollection();
        $this->myfriendships = new ArrayCollection();
        $this->ratingRessources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTimeInterface $resetTokenExpiresAt): static
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;

        return $this;
    }

    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?string $verificationCode): static
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    public function getCodeExpirationDate(): ?\DateTimeInterface
    {
        return $this->codeExpirationDate;
    }

    public function setCodeExpirationDate(?\DateTimeInterface $codeExpirationDate): static
    {
        $this->codeExpirationDate = $codeExpirationDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAccountVerification(): ?string
    {
        return $this->accountVerification;
    }

    public function setAccountVerification(string $accountVerification): static
    {
        $this->accountVerification = $accountVerification;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUserNotif($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUserNotif() === $this) {
                $notification->setUserNotif(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getToNotifications(): Collection
    {
        return $this->toNotifications;
    }

    public function addToNotification(Notification $toNotification): static
    {
        if (!$this->toNotifications->contains($toNotification)) {
            $this->toNotifications->add($toNotification);
            $toNotification->setToUserNotif($this);
        }

        return $this;
    }

    public function removeToNotification(Notification $toNotification): static
    {
        if ($this->toNotifications->removeElement($toNotification)) {
            // set the owning side to null (unless already changed)
            if ($toNotification->getToUserNotif() === $this) {
                $toNotification->setToUserNotif(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->addParticipant($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            $event->removeParticipant($this);
        }

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): static
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations->add($conversation);
            $conversation->addPersonne($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): static
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removePersonne($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }

        return $this;
    }

    public function getFailedLoginAttempts(): ?int
    {
        return $this->failedLoginAttempts;
    }

    public function setFailedLoginAttempts(?int $failedLoginAttempts): static
    {
        $this->failedLoginAttempts = $failedLoginAttempts;

        return $this;
    }

    public function getLockUntil(): ?\DateTimeInterface
    {
        return $this->lockUntil;
    }

    public function setLockUntil(?\DateTimeInterface $lockUntil): static
    {
        $this->lockUntil = $lockUntil;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function is2FA(): ?bool
    {
        return $this->is2FA;
    }

    public function setIs2FA(bool $is2FA): static
    {
        $this->is2FA = $is2FA;

        return $this;
    }

    public function getCode2FA(): ?string
    {
        return $this->code2FA;
    }

    public function setCode2FA(?string $code2FA): static
    {
        $this->code2FA = $code2FA;

        return $this;
    }

    public function getCode2FAexpiry(): ?\DateTimeInterface
    {
        return $this->code2FAexpiry;
    }

    public function setCode2FAexpiry(?\DateTimeInterface $code2FAexpiry): static
    {
        $this->code2FAexpiry = $code2FAexpiry;

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
            $ressource->setUserId($this);
        }

        return $this;
    }

    public function removeRessource(Ressources $ressource): static
    {
        if ($this->ressources->removeElement($ressource)) {
            // set the owning side to null (unless already changed)
            if ($ressource->getUserId() === $this) {
                $ressource->setUserId(null);
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
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setIdUser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getIdUser() === $this) {
                $reclamation->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Demandes>
     */
    public function getTodemandes(): Collection
    {
        return $this->todemandes;
    }

    public function addTodemande(Demandes $todemande): static
    {
        if (!$this->todemandes->contains($todemande)) {
            $this->todemandes->add($todemande);
            $todemande->setToUser($this);
        }

        return $this;
    }

    public function removeTodemande(Demandes $todemande): static
    {
        if ($this->todemandes->removeElement($todemande)) {
            // set the owning side to null (unless already changed)
            if ($todemande->getToUser() === $this) {
                $todemande->setToUser(null);
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
     * @return Collection<int, Pointrecyclage>
     */
    public function getPointrecyclages(): Collection
    {
        return $this->pointrecyclages;
    }

    public function addPointrecyclage(Pointrecyclage $pointrecyclage): static
    {
        if (!$this->pointrecyclages->contains($pointrecyclage)) {
            $this->pointrecyclages->add($pointrecyclage);
            $pointrecyclage->setOwner($this);
        }

        return $this;
    }

    public function removePointrecyclage(Pointrecyclage $pointrecyclage): static
    {
        if ($this->pointrecyclages->removeElement($pointrecyclage)) {
            // set the owning side to null (unless already changed)
            if ($pointrecyclage->getOwner() === $this) {
                $pointrecyclage->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUserCommented($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUserCommented() === $this) {
                $comment->setUserCommented(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Produitrecyclage>
     */
    public function getProduitrecyclages(): Collection
    {
        return $this->produitrecyclages;
    }

    public function addProduitrecyclage(Produitrecyclage $produitrecyclage): static
    {
        if (!$this->produitrecyclages->contains($produitrecyclage)) {
            $this->produitrecyclages->add($produitrecyclage);
            $produitrecyclage->setUser($this);
        }

        return $this;
    }

    public function removeProduitrecyclage(Produitrecyclage $produitrecyclage): static
    {
        if ($this->produitrecyclages->removeElement($produitrecyclage)) {
            // set the owning side to null (unless already changed)
            if ($produitrecyclage->getUser() === $this) {
                $produitrecyclage->setUser(null);
            }
        }

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getFriendships(): Collection
    {
        return $this->friendships;
    }

    public function addFriendship(Friendship $friendship): static
    {
        if (!$this->friendships->contains($friendship)) {
            $this->friendships->add($friendship);
            $friendship->setUser($this);
        }

        return $this;
    }

    public function removeFriendship(Friendship $friendship): static
    {
        if ($this->friendships->removeElement($friendship)) {
            // set the owning side to null (unless already changed)
            if ($friendship->getUser() === $this) {
                $friendship->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getMyfriendships(): Collection
    {
        return $this->myfriendships;
    }

    public function addMyfriendship(Friendship $myfriendship): static
    {
        if (!$this->myfriendships->contains($myfriendship)) {
            $this->myfriendships->add($myfriendship);
            $myfriendship->setFriend($this);
        }

        return $this;
    }

    public function removeMyfriendship(Friendship $myfriendship): static
    {
        if ($this->myfriendships->removeElement($myfriendship)) {
            // set the owning side to null (unless already changed)
            if ($myfriendship->getFriend() === $this) {
                $myfriendship->setFriend(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RatingRessource>
     */
    public function getRatingRessources(): Collection
    {
        return $this->ratingRessources;
    }

    public function addRatingRessource(RatingRessource $ratingRessource): static
    {
        if (!$this->ratingRessources->contains($ratingRessource)) {
            $this->ratingRessources->add($ratingRessource);
            $ratingRessource->setUser($this);
        }

        return $this;
    }

    public function removeRatingRessource(RatingRessource $ratingRessource): static
    {
        if ($this->ratingRessources->removeElement($ratingRessource)) {
            // set the owning side to null (unless already changed)
            if ($ratingRessource->getUser() === $this) {
                $ratingRessource->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getUnreadToNotifications(): Collection
    {
        return $this->toNotifications->filter(function (Notification $notification) {
            return !$notification->isRead();
        });
    }

    public function getFriendsCount(): int
    {
        return $this->friendships->count() + $this->myfriendships->count();
    }
}
