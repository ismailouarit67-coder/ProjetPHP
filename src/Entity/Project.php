<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
#[Assert\Length(max: 150)]

    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'L’objectif doit être supérieur à 0.')]

    private ?float $goalAmount = null;

    #[ORM\Column]
    private ?float $currentAmount = null;

    #[ORM\Column]
    #[Assert\NotNull]

    private ?\DateTime $endDate = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Contribution>
     */
    #[ORM\OneToMany(targetEntity: Contribution::class, mappedBy: 'project')]
    private Collection $contributions;
public function __construct()
{
    $this->currentAmount = 0;
    $this->status = 'active';
    $this->createdAt = new \DateTimeImmutable();
    $this->contributions = new ArrayCollection();
}

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getGoalAmount(): ?float
    {
        return $this->goalAmount;
    }

    public function setGoalAmount(float $goalAmount): static
    {
        $this->goalAmount = $goalAmount;

        return $this;
    }

    public function getCurrentAmount(): ?float
    {
        return $this->currentAmount;
    }

    public function setCurrentAmount(float $currentAmount): static
    {
        $this->currentAmount = $currentAmount;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Contribution>
     */
    public function getContributions(): Collection
    {
        return $this->contributions;
    }

    public function addContribution(Contribution $contribution): static
    {
        if (!$this->contributions->contains($contribution)) {
            $this->contributions->add($contribution);
            $contribution->setProject($this);
        }

        return $this;
    }

    public function removeContribution(Contribution $contribution): static
    {
        if ($this->contributions->removeElement($contribution)) {
            // set the owning side to null (unless already changed)
            if ($contribution->getProject() === $this) {
                $contribution->setProject(null);
            }
        }

        return $this;
    }
}
