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

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $quantity = null;

    #[ORM\ManyToOne] 
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'stepProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Step $step = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'stepProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStep(): ?Step
    {
        return $this->step;
    }

    public function setStep(?Step $step): static
    {
        $this->step = $step;
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

    /**
     * Get the value of unit
     */
    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    /**
     * Set the value of unit
     *
     * @return  self
     */
    public function setUnit(?Unit $unit): static
    {
        $this->unit = $unit;

        return $this;
    }   
}
