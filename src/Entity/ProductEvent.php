<?php

namespace App\Entity;

use App\Repository\ProductEventRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductEventRepository::class)]
class ProductEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'productEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SaleEvent $event = null;

    #[ORM\Column]
    private ?int $quantity;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $outOfStockDateTime = null;

    #[ORM\Column(nullable: true)]
    private ?int $unsoldQuantity = null;

    #[ORM\Column(nullable: true)]
    private ?float $lotPrice = null;

    #[ORM\Column(nullable: true)]
    private ?float $unsoldPrice = null;    

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEvent(): ?SaleEvent
    {
        return $this->event;
    }

    public function setEvent(?SaleEvent $event): static
    {
        $this->event = $event;

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

    public function getOutOfStockDateTime(): ?DateTimeImmutable
    {
        return $this->outOfStockDateTime;
    }

    public function setOutOfStockDateTime(?\DateTimeImmutable $outOfStockDateTime): static
    {
        $this->outOfStockDateTime = $outOfStockDateTime;
        return $this;
    }

    public function markAsOutOfStockForEvent(): static
    {
        $this->setUnsoldQuantity(0);
        if ($this->outOfStockDateTime === null) {
            $this->setOutOfStockDateTime(new DateTimeImmutable());
        }
        return $this;
    }

    public function getUnsoldQuantity(): ?int
    {
        return $this->unsoldQuantity;
    }

    public function setUnsoldQuantity(?int $unsoldQuantity): static
    {
        $this->unsoldQuantity = $unsoldQuantity;

        return $this;
    }

    public function getUnsoldPrice(): ?float
    {
        return $this->unsoldPrice;
    }    

    public function setUnsoldPrice(?float $unsoldPrice): static
    {
        $this->unsoldPrice = $unsoldPrice;

        return $this;
    }

    public function getLotPrice(): ?float
    {
        return $this->lotPrice;
    }

    public function setLotPrice(float $lotPrice): static
    {
        $this->lotPrice = $lotPrice;

        return $this;
    }

    public function updateLotPrice(): static
    {
        $this->lotPrice = $this->getQuantity() * $this->getProduct()->getPrice();
        return $this;
    }
}
