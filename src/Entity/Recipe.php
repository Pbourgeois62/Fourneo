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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $yield = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, RecipeProduct>
     */
    // Les ingrédients de cette recette. Ces RecipeProduct pointeront vers des Products.
    #[ORM\OneToMany(targetEntity: RecipeProduct::class, mappedBy: 'recipe', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $recipeProducts;

    /**
     * @var Collection<int, Step>
     */
    #[ORM\OneToMany(targetEntity: Step::class, mappedBy: 'recipe', cascade: ['persist', 'remove'])]
    private Collection $steps;

    // Relation OneToOne **obligatoire** : Chaque recette doit produire un Product
    // 'remove' si vous voulez supprimer le produit si la recette est supprimée.
    // 'persist' est nécessaire pour sauvegarder le produit quand la recette est sauvegardée.
    #[ORM\OneToOne(targetEntity: \App\Entity\Product::class, mappedBy: 'recipe', cascade: ['persist', 'remove'])]
    private ?\App\Entity\Product $productResult = null; // Le Product que cette recette produit

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->recipeProducts = new ArrayCollection();
        $this->steps = new ArrayCollection();
        // Pas de $this->subRecipes = new ArrayCollection();
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

    public function getYield(): ?int
    {
        return $this->yield;
    }

    public function setYield(int $yield): static
    {
        $this->yield = $yield;
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

    public function getTotalCost(): float
    {
        $totalCost = 0.0;
        foreach ($this->getRecipeProducts() as $recipeProduct) {
            $product = $recipeProduct->getProduct();
            $quantity = $recipeProduct->getQuantity();
            if ($product && method_exists($product, 'getCost') && $quantity !== null) {
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

    public function getProductResult(): ?Product
    {
        return $this->productResult;
    }

    public function setProductResult(?Product $productResult): static
    {
        if ($productResult === null && $this->productResult !== null) {
            $this->productResult->setRecipe(null);
        }

        if ($productResult !== null && $productResult->getRecipe() !== $this) {
            $productResult->setRecipe($this);
        }

        $this->productResult = $productResult;

        return $this;
    }
}