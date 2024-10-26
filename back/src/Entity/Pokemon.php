<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Pokemon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[ORM\Column(type: 'integer')]
    private ?int $height = null;

    #[ORM\Column(type: 'integer')]
    private ?int $weight = null;

    #[ORM\Column(type: 'json')]
    private array $types = [];

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $frontDefault = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $backDefault = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $abilities = [];

    // Getters and Setters
    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getHeight(): ?int { return $this->height; }
    public function setHeight(int $height): self { $this->height = $height; return $this; }

    public function getWeight(): ?int { return $this->weight; }
    public function setWeight(int $weight): self { $this->weight = $weight; return $this; }

    public function getTypes(): array { return $this->types; }
    public function setTypes(array $types): self { $this->types = $types; return $this; }

    public function getFrontDefault(): ?string { return $this->frontDefault; }
    public function setFrontDefault(?string $frontDefault): self { $this->frontDefault = $frontDefault; return $this; }

    public function getBackDefault(): ?string { return $this->backDefault; }
    public function setBackDefault(?string $backDefault): self { $this->backDefault = $backDefault; return $this; }

    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function setImageUrl(?string $imageUrl): self { $this->imageUrl = $imageUrl; return $this; }

    public function getAbilities(): array { return $this->abilities; }
    public function setAbilities(array $abilities): self { $this->abilities = $abilities; return $this; }
}
