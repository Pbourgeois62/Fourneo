<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RecipeProductRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: RecipeProductRepository::class)]
class RecipeProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'recipeProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe = null;

    #[ORM\ManyToOne(inversedBy: 'recipeProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 50)]
    private ?string $unit = null;

    #[ORM\ManyToOne(inversedBy: 'subRecipes ')] 
    #[ORM\JoinColumn(nullable: true)]
    private ?Recipe $subRecipe = null;

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, mixed $payload)
    {
        if (($this->product !== null) && ($this->subRecipe !== null)) {
            $context->buildViolation('Un ingrédient ne peut pas être à la fois un produit et une sous-recette.')
                ->atPath('product')
                ->addViolation();
        }

        if (($this->product === null) && ($this->subRecipe === null)) {
            $context->buildViolation('Un ingrédient doit être soit un produit, soit une sous-recette.')
                ->atPath('product')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getSubRecipe(): ?Recipe
    {
        return $this->subRecipe;
    }

    public function setSubRecipe(?Recipe $subRecipe): static
    {
        $this->subRecipe = $subRecipe;
        if ($subRecipe !== null) {
            $this->product = null;
        }

        return $this;
    }

    public function getName(): string
    {
        if ($this->product !== null) {
            return $this->product->getName();
        } elseif ($this->subRecipe !== null) {
            return $this->subRecipe->getName() . ' (recette)'; 
        }
        return 'Ingrédient inconnu'; 
    }
}