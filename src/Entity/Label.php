<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LabelRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[ORM\Entity(repositoryClass: LabelRepository::class)]
#[Uploadable]
class Label
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[UploadableField(mapping: 'label', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;   

    #[ORM\ManyToOne(inversedBy: 'labels')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Product $produit = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): static
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->produit;
    }

    public function setProduct(?Product $produit): static
    {
        $this->produit = $produit;

        return $this;
    }
    
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
    public function __toString(): string
    {
        // Retourne une représentation unique et utile du Label.
        // Cela sera utilisé par défaut si 'choice_label' n'est pas spécifié.
        // Peut être risqué si le label n'a pas encore d'ID ou si vous avez besoin de formats différents.
        return (string) $this->getId() . ' - Créé le ' . $this->createdAt->format('Y-m-d H:i');
    }

    // Option 2B: Méthode dédiée pour le formatage de la date
    public function getFormattedCreatedAt(): string
    {
        if (null === $this->createdAt) {
            return ''; // Ou une valeur par défaut
        }
        return $this->createdAt->format('d/m/Y H:i'); // Format de date lisible
    }
}
