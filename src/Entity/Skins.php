<?php

namespace App\Entity;

use JsonSerializable;
use App\Repository\SkinsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkinsRepository::class)]
class Skins implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $main_font = null;

    #[ORM\Column(length: 255)]
    private ?string $base_font = null;

    #[ORM\Column(length: 255)]
    private ?string $icon = null;


    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'base_font' => $this->base_font,
            'main_font' => $this->main_font,
            'icon' => $this->icon,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMainFont(): ?string
    {
        return $this->main_font;
    }

    public function setMainFont(string $main_font): self
    {
        $this->main_font = $main_font;

        return $this;
    }

    public function getBaseFont(): ?string
    {
        return $this->base_font;
    }

    public function setBaseFont(string $base_font): self
    {
        $this->base_font = $base_font;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }
}
