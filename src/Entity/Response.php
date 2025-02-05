<?php

namespace App\Entity;

use App\Repository\ResponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponseRepository::class)]
class Response
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $ResponseId = null;

    #[ORM\Column(length: 255)]
    private ?string $Content = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $DateResponse = null;

    #[ORM\ManyToOne(inversedBy: 'responses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Complaints $ComplaintId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResponseId(): ?int
    {
        return $this->ResponseId;
    }

    public function setResponseId(int $ResponseId): static
    {
        $this->ResponseId = $ResponseId;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(string $Content): static
    {
        $this->Content = $Content;

        return $this;
    }

    public function getDateResponse(): ?\DateTimeImmutable
    {
        return $this->DateResponse;
    }

    public function setDateResponse(\DateTimeImmutable $DateResponse): static
    {
        $this->DateResponse = $DateResponse;

        return $this;
    }

    public function getComplaintId(): ?Complaints
    {
        return $this->ComplaintId;
    }

    public function setComplaintId(?Complaints $ComplaintId): static
    {
        $this->ComplaintId = $ComplaintId;

        return $this;
    }
}
