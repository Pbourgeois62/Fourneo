<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?float $yield = null;

    #[ORM\ManyToOne(targetEntity: Unit::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Unit $yieldUnit = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $isSubRecipe = false;

    #[ORM\OneToMany(targetEntity: Step::class, mappedBy: 'recipe', cascade: ['persist', 'remove'])]
    private Collection $steps;

    #[ORM\OneToOne(targetEntity: Product::class, mappedBy: 'recipe', cascade: ['persist', 'remove'])]
    private ?Product $productResult = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->steps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getYield(): ?float
    {
        return $this->yield;
    }

    public function setYield(?float $yield): static
    {
        $this->yield = $yield;
        return $this;
    }

    public function getYieldUnit(): ?Unit
    {
        return $this->yieldUnit;
    }

    public function setYieldUnit(?Unit $yieldUnit): static
    {
        $this->yieldUnit = $yieldUnit;
        return $this;
    }

    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): static
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
            $step->setRecipe($this);
        }
        return $this;
    }

    public function removeStep(Step $step): static
    {
        if ($this->steps->removeElement($step)) {
            if ($step->getRecipe() === $this) {
                $step->setRecipe(null);
            }
        }
        return $this;
    }

    public function getTotalDuration(): int
    {
        $totalDuration = 0;
        foreach ($this->getSteps() as $step) {
            $duration = $step->getDurationMinutes();
            if ($duration !== null) {
                $totalDuration += $duration;
            }
        }
        return $totalDuration;
    }

    public function getProductResult(): ?Product
    {
        return $this->productResult;
    }

    public function setProductResult(?Product $productResult): static
    {
        $this->productResult = $productResult;
        return $this;
    }

    public function getIsSubRecipe(): ?bool
    {
        return $this->isSubRecipe;
    }

    public function setIsSubRecipe(?bool $isSubRecipe): static
    {
        $this->isSubRecipe = $isSubRecipe;
        return $this;
    }
}
