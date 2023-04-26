<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity]
class Coffee
{
    #[SerializedName('entity_id')]
    #[ORM\Id, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $categoryName;

    #[ORM\Column(type: 'string')]
    private string $sku;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'text')]
    private string $shortDesc;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $price;

    #[ORM\Column(type: 'string')]
    private string $link;

    #[ORM\Column(type: 'string')]
    private string $image;

    #[ORM\Column(type: 'string')]
    private string $brand;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rating;

    #[ORM\Column(type: 'string')]
    private string $caffeineType;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $count;

    #[ORM\Column(type: 'string')]
    private string $flavored;

    #[ORM\Column(type: 'string')]
    private string $seasonal;

    #[ORM\Column(type: 'string')]
    private string $inStock;

    #[ORM\Column(type: 'boolean')]
    private bool $facebook;

    #[ORM\Column(type: 'boolean')]
    private bool $isKCup;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): void
    {
        $this->categoryName = $categoryName;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getShortDesc(): string
    {
        return $this->shortDesc;
    }

    public function setShortDesc(string $shortDesc): void
    {
        $this->shortDesc = $shortDesc;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = '' === $price ? null : $price;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating($rating): void
    {
        $this->rating = '' === $rating ? null : $rating;
    }

    public function getCaffeineType(): string
    {
        return $this->caffeineType;
    }

    public function setCaffeineType(string $caffeineType): void
    {
        $this->caffeineType = $caffeineType;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount($count): void
    {
        $this->count = '' === $count ? null : $count;
    }

    public function getFlavored(): string
    {
        return $this->flavored;
    }

    public function setFlavored(string $flavored): void
    {
        $this->flavored = $flavored;
    }

    public function getSeasonal(): string
    {
        return $this->seasonal;
    }

    public function setSeasonal(string $seasonal): void
    {
        $this->seasonal = $seasonal;
    }

    public function getInStock(): string
    {
        return $this->inStock;
    }

    public function setInStock(string $inStock): void
    {
        $this->inStock = $inStock;
    }

    public function isFacebook(): bool
    {
        return $this->facebook;
    }

    public function setFacebook(bool $facebook): void
    {
        $this->facebook = $facebook;
    }

    public function isKCup(): bool
    {
        return $this->isKCup;
    }

    public function setIsKCup(bool $isKCup): void
    {
        $this->isKCup = $isKCup;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('categoryName', new Assert\Length(['max' => 255]));
        $metadata->addPropertyConstraint('name', new Assert\Length(['max' => 255]));
        $metadata->addPropertyConstraint('sku', new Assert\Length(['max' => 255]));
        $metadata->addPropertyConstraint('link', new Assert\Length(['max' => 255]));
        $metadata->addPropertyConstraint('image', new Assert\Length(['max' => 255]));
        $metadata->addPropertyConstraint('brand', new Assert\Length(['max' => 255]));
        $metadata->addPropertyConstraint('caffeineType', new Assert\Length(['max' => 255]));
        $metadata->addPropertyConstraint('flavored', new Assert\Length(['max' => 255]));
        $metadata->addPropertyConstraint('seasonal', new Assert\Length(['max' => 255]));
        $metadata->addPropertyConstraint('inStock', new Assert\Length(['max' => 255]));
    }
}
