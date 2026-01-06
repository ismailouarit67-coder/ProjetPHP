<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\ContributionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContributionRepository::class)]
class Contribution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Le montant doit être supérieur à 0.')]

    private ?float $amount = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $donorName = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $contributedAt = null;

    #[ORM\ManyToOne(inversedBy: 'contributions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?project $project = null;
    public function __construct()
{
    $this->contributedAt = new \DateTimeImmutable();
}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDonorName(): ?string
    {
        return $this->donorName;
    }

    public function setDonorName(?string $donorName): static
    {
        $this->donorName = $donorName;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getContributedAt(): ?\DateTimeImmutable
    {
        return $this->contributedAt;
    }

    public function setContributedAt(\DateTimeImmutable $contributedAt): static
    {
        $this->contributedAt = $contributedAt;

        return $this;
    }

    public function getProject(): ?project
    {
        return $this->project;
    }

    public function setProject(?project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
