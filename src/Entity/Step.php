<?php

namespace App\Entity;

use App\Repository\StepRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\StepProduct;

#[ORM\Entity(repositoryClass: StepRepository::class)]
class Step
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;    

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $durationMinutes = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $number = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe = null;

    /**
     * @var Collection<int, StepProduct>
     */
    #[ORM\OneToMany(targetEntity: StepProduct::class, mappedBy: 'step', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $stepProducts;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->stepProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(?int $durationMinutes): self
    {
        $this->durationMinutes = $durationMinutes;
        return $this;
    }

    /**
     * Méthode utilitaire pour afficher la durée de manière lisible.
     */
    public function getFormattedDuration(): ?string
    {
        if ($this->durationMinutes === null) {
            return null;
        }

        $totalMinutes = $this->durationMinutes;

        if ($totalMinutes < 60) {
            return $totalMinutes . ' min';
        } else {
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            $formatted = '';
            if ($hours > 0) {
                $formatted .= $hours . ' h';
            }
            if ($minutes > 0) {
                if ($hours > 0) {
                    $formatted .= ' ';
                }
                $formatted .= $minutes . ' min';
            }
            if ($hours > 0 && $minutes === 0) {
                return $hours . ' h';
            }

            return trim($formatted);
        }
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return Collection<int, StepProduct>
     */
    public function getStepProducts(): Collection
    {
        return $this->stepProducts;
    }

    public function addStepProduct(StepProduct $stepProduct): static
    {
        if (!$this->stepProducts->contains($stepProduct)) {
            $this->stepProducts->add($stepProduct);
            $stepProduct->setStep($this);
        }

        return $this;
    }

    public function removeStepProduct(StepProduct $stepProduct): static
    {
        if ($this->stepProducts->removeElement($stepProduct)) {
            // set the owning side to null (unless already changed)
            if ($stepProduct->getStep() === $this) {
                $stepProduct->setStep(null);
            }
        }

        return $this;
    }
}
