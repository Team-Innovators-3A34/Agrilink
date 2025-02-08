<?php

namespace App\Entity;

use App\Repository\NotificationRepository;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column]
    private ?bool $isRead = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?User $userNotif = null;

    #[ORM\Column(length: 255)]
    private ?string $typeNotification = null;

    #[ORM\ManyToOne(inversedBy: 'toNotifications')]
    private ?User $toUserNotif = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserNotif(): ?User
    {
        return $this->userNotif;
    }

    public function setUserNotif(?User $userNotif): static
    {
        $this->userNotif = $userNotif;

        return $this;
    }

    public function getTypeNotification(): ?string
    {
        return $this->typeNotification;
    }

    public function setTypeNotification(string $typeNotification): static
    {
        $this->typeNotification = $typeNotification;

        return $this;
    }

    public function getToUserNotif(): ?User
    {
        return $this->toUserNotif;
    }

    public function setToUserNotif(?User $toUserNotif): static
    {
        $this->toUserNotif = $toUserNotif;

        return $this;
    }
}
