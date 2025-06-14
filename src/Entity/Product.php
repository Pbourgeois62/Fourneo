<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $size = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    /**
     * @var Collection<int, ProductEvent>
     */
    #[ORM\OneToMany(targetEntity: ProductEvent::class, mappedBy: 'product')]
    private Collection $productEvents;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->productEvents = new ArrayCollection();
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

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;

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
     * @return Collection<int, ProductEvent>
     */
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
}