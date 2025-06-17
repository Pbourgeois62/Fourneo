<?php

namespace App\Entity;

use App\Repository\SaleEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleEventRepository::class)]
class SaleEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $address = null;

     #[ORM\Column(length: 255, nullable: true)]
    private ?string $weather = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startDate;

    #[ORM\Column]
    private ?\DateTimeImmutable $endDate;    

    /**
     * @var Collection<int, ProductEvent>
     */
    #[ORM\OneToMany(targetEntity: ProductEvent::class, mappedBy: 'event', orphanRemoval: true, cascade: ['persist'])]
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

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

    public function getWeather(): ?string
    {
        return $this->weather;
    }

    public function setWeather(?string $weather): static
    {
        $this->weather = $weather;

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
            $productEvent->setEvent($this);
        }

        return $this;
    }

    public function removeProductEvent(ProductEvent $productEvent): static
    {
        if ($this->productEvents->removeElement($productEvent)) {
            // set the owning side to null (unless already changed)
            if ($productEvent->getEvent() === $this) {
                $productEvent->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return int Le nombre total de ProductEvents associés à cette vente.
     */
    public function getTotalProductEventCount(): int
    {
        return $this->productEvents->count();
    }

    /**
     * @return int Le nombre de ProductEvents pour lesquels unsoldQuantity est 0.
     */
    public function getOutOfStockProductEventCount(): int
    {
        $count = 0;
        foreach ($this->productEvents as $productEvent) {
            if ($productEvent->getUnsoldQuantity() === 0) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * @return int La somme des quantités invendues pour tous les ProductEvents.
     */
    public function getTotalUnsoldQuantity(): int
    {
        $totalUnsold = 0;
        foreach ($this->productEvents as $productEvent) {
            // Assurez-vous que unsoldQuantity n'est pas null avant d'additionner
            $totalUnsold += $productEvent->getUnsoldQuantity() ?? 0;
        }
        return $totalUnsold;
    }

    /**
     * @return float Le pourcentage de produits invendus par rapport à la quantité initiale totale.
     */
    public function getUnsoldPercentage(): float
    {
        $totalInitialQuantity = 0;
        foreach ($this->productEvents as $productEvent) {
            $totalInitialQuantity += $productEvent->getQuantity();
        }

        if ($totalInitialQuantity === 0) {
            return 0.0; // Évite la division par zéro
        }

        return ($this->getTotalUnsoldQuantity() / $totalInitialQuantity) * 100;
    }

    /**
     * @return float Le pourcentage de produits en rupture de stock.
     */
    public function getOutOfStockPercentage(): float
    {
        $totalProductEvents = $this->getTotalProductEventCount();
        if ($totalProductEvents === 0) {
            return 0.0;
        }
        return ($this->getOutOfStockProductEventCount() / $totalProductEvents) * 100;
    }

     public function getTotalRevenue(): ?float
    {
        $totalRevenue = 0;
        foreach ($this->productEvents as $productEvent) {
            $totalRevenue += $productEvent->getLotPrice();
        }
        return $totalRevenue;
    }
}
