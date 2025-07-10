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

    #[ORM\Column]
    private ?string $yield = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, RecipeProduct>
     */
    #[ORM\OneToMany(targetEntity: RecipeProduct::class, mappedBy: 'recipe', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $recipeProducts;

    /**
     * @var Collection<int, Step>
     */
    #[ORM\OneToMany(targetEntity: Step::class, mappedBy: 'recipe', cascade: ['persist', 'remove'])]
    private Collection $steps;

    /**
     * @var Collection<int, RecipeProduct>
     */
    #[ORM\OneToMany(targetEntity: RecipeProduct::class, mappedBy: 'subRecipe')] // CHANGEMENT ICI : mappedBy: 'subRecipe'
    private Collection $subRecipes;
    
    #[ORM\OneToOne(targetEntity: \App\Entity\Product::class, mappedBy: 'recipe', cascade: ['persist', 'remove'])]
    private ?\App\Entity\Product $productResult = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->recipeProducts = new ArrayCollection();
        $this->steps = new ArrayCollection();
        $this->subRecipes = new ArrayCollection();
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

    /**
     * @return Collection<int, RecipeProduct>
     */
    public function getRecipeProducts(): Collection
    {
        return $this->recipeProducts;
    }

    public function addRecipeProduct(RecipeProduct $recipeProduct): static
    {
        if (!$this->recipeProducts->contains($recipeProduct)) {
            $this->recipeProducts->add($recipeProduct);
            $recipeProduct->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeProduct(RecipeProduct $recipeProduct): static
    {
        if ($this->recipeProducts->removeElement($recipeProduct)) {
            if ($recipeProduct->getRecipe() === $this) {
                $recipeProduct->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Step>
     */
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

    public function getYield()
    {
        return $this->yield;
    }

    public function setYield($yield)
    {
        $this->yield = $yield;

        return $this;
    }

    public function getTotalCost(): float
    {
        $totalCost = 0.0;

        foreach ($this->getRecipeProducts() as $recipeProduct) {
            $product = $recipeProduct->getProduct();
            $quantity = $recipeProduct->getQuantity();

            if ($product && $product->getPrice() !== null && $quantity !== null) {
                $totalCost += $product->getPrice() * $quantity;
            }
        }

        return $totalCost;
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

    /**
     * @return Collection<int, RecipeProduct>
     */
    public function getSubRecipes(): Collection // CHANGEMENT ICI : Getter pour 'subRecipes'
    {
        return $this->subRecipes;
    }

    public function addSubRecipe(RecipeProduct $subRecipe): static // CHANGEMENT ICI : Méthode add pour 'subRecipes'
    {
        if (!$this->subRecipes->contains($subRecipe)) {
            $this->subRecipes->add($subRecipe);
            $subRecipe->setSubRecipe($this);
        }

        return $this;
    }

    public function removeSubRecipe(RecipeProduct $subRecipe): static // CHANGEMENT ICI : Méthode remove pour 'subRecipes'
    {
        if ($this->subRecipes->removeElement($subRecipe)) {
            // set the owning side to null (unless already changed)
            if ($subRecipe->getSubRecipe() === $this) {
                $subRecipe->setSubRecipe(null);
            }
        }

        return $this;
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
}