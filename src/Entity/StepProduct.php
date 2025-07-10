<?php

namespace App\Entity;

use App\Repository\StepProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StepProductRepository::class)]
class StepProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Relation ManyToOne vers Step
    #[ORM\ManyToOne(inversedBy: 'stepProducts')] // ou 'stepProducts' si vous l'avez nommé ainsi dans Step
    #[ORM\JoinColumn(nullable: false)]
    private ?Step $step = null;

    // Relation ManyToOne vers RecipeProduct (l'ingrédient global de la recette)
    #[ORM\ManyToOne(targetEntity: RecipeProduct::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?RecipeProduct $recipeProduct = null;

    #[ORM\Column(nullable: true)]
    private ?float $quantity = null;

    // --- CONSTRUCTEUR ---
    public function __construct()
    {
        // Initialisation si nécessaire
    }

    // --- GETTERS & SETTERS ---
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStep(): ?Step
    {
        return $this->step;
    }

    public function setStep(?Step $step): static
    {
        $this->step = $step;
        return $this;
    }

    public function getRecipeProduct(): ?RecipeProduct
    {
        return $this->recipeProduct;
    }

    public function setRecipeProduct(?RecipeProduct $recipeProduct): static
    {
        $this->recipeProduct = $recipeProduct;
        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }
}