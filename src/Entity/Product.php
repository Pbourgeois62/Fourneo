<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du produit ne peut pas être vide.')]
    private ?string $name = null;

    #[ORM\Column(type: 'float')]
    #[Assert\PositiveOrZero(message: 'Le prix doit être positif ou zéro.')]
    private ?float $price = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private ?bool $isRecipeResult = false;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $size = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Label::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $labels;

    #[ORM\OneToMany(targetEntity: ProductEvent::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $productEvents;

    #[ORM\ManyToMany(targetEntity: Allergen::class, inversedBy: 'products')]
    private Collection $allergens;

    #[ORM\ManyToMany(targetEntity: DeliveryNote::class, inversedBy: 'products')]
    private Collection $deliveryNotes;

    #[ORM\OneToMany(targetEntity: RecipeProduct::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $usedInRecipeProducts;

    #[ORM\OneToOne(targetEntity: \App\Entity\Recipe::class, inversedBy: 'productResult', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?\App\Entity\Recipe $recipe = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->productEvents = new ArrayCollection();
        $this->allergens = new ArrayCollection();
        $this->labels = new ArrayCollection();
        $this->deliveryNotes = new ArrayCollection();
        $this->usedInRecipeProducts = new ArrayCollection();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function isRecipeResult(): ?bool
    {
        return $this->isRecipeResult;
    }

    public function setIsRecipeResult(?bool $isRecipeResult): static
    {
        $this->isRecipeResult = $isRecipeResult;
        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getLabels(): Collection
    {
        return $this->labels;
    }

    public function addLabel(Label $label): static
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
            $label->setProduct($this);
        }
        return $this;
    }

    public function removeLabel(Label $label): static
    {
        if ($this->labels->removeElement($label)) {
            if ($label->getProduct() === $this) {
                $label->setProduct(null);
            }
        }
        return $this;
    }

    public function getProductEvents(): Collection
    {
        return $this->productEvents;
    }

    public function addProductEvent(ProductEvent $productEvent): static
    {
        if (!$this->productEvents->contains($productEvent)) {
            $this->productEvents->add($productEvent);
            $productEvent->setProduct($this);
        }
        return $this;
    }

    public function removeProductEvent(ProductEvent $productEvent): static
    {
        if ($this->productEvents->removeElement($productEvent)) {
            if ($productEvent->getProduct() === $this) {
                $productEvent->setProduct(null);
            }
        }
        return $this;
    }

    public function getAllergens(): Collection
    {
        return $this->allergens;
    }

    public function addAllergen(Allergen $allergen): static
    {
        if (!$this->allergens->contains($allergen)) {
            $this->allergens->add($allergen);
        }
        return $this;
    }

    public function removeAllergen(Allergen $allergen): static
    {
        $this->allergens->removeElement($allergen);
        return $this;
    }

    public function getDeliveryNotes(): Collection
    {
        return $this->deliveryNotes;
    }

    public function addDeliveryNote(DeliveryNote $deliveryNote): static
    {
        if (!$this->deliveryNotes->contains($deliveryNote)) {
            $this->deliveryNotes->add($deliveryNote);
            if (!$deliveryNote->getProducts()->contains($this)) {
                $deliveryNote->addProduct($this);
            }
        }
        return $this;
    }

    public function removeDeliveryNote(DeliveryNote $deliveryNote): static
    {
        if ($this->deliveryNotes->removeElement($deliveryNote)) {
            if ($deliveryNote->getProducts()->contains($this)) {
                $deliveryNote->removeProduct($this);
            }
        }
        return $this;
    }

    public function getUsedInRecipeProducts(): Collection
    {
        return $this->usedInRecipeProducts;
    }

    public function addUsedInRecipeProduct(RecipeProduct $usedInRecipeProduct): static
    {
        if (!$this->usedInRecipeProducts->contains($usedInRecipeProduct)) {
            $this->usedInRecipeProducts->add($usedInRecipeProduct);
            $usedInRecipeProduct->setProduct($this);
        }
        return $this;
    }

    public function removeUsedInRecipeProduct(RecipeProduct $usedInRecipeProduct): static
    {
        if ($this->usedInRecipeProducts->removeElement($usedInRecipeProduct)) {
            if ($usedInRecipeProduct->getProduct() === $this) {
                $usedInRecipeProduct->setProduct(null);
            }
        }
        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;
        if ($recipe !== null) {
            $this->isRecipeResult = true;
        } else {
            $this->isRecipeResult = false;
        }
        return $this;
    }

    public function getDisplayName(): string
    {
        if ($this->isRecipeResult()) {
            return $this->getName() . ' (Recette)';
        }
        return $this->getName();
    }
}