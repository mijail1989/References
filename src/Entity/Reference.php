<?php

namespace App\Entity;

use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReferenceRepository;

#[ORM\Entity(repositoryClass: ReferenceRepository::class)]
class Reference implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    private ?string $name = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lang = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img = null;

    #[ORM\ManyToOne(inversedBy: 'refs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'lang' => $this->lang,
            'description' => $this->description,
            'img' => $this->img,
            'user'=>$this->user
        ];
    }
    
    public function __toString(): string
    {
        return $this->name;
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
    
    public function getUrl(): ?string
    {
        return $this->url;
    }
    
    public function setUrl(?string $url): self
    {
        $this->url = $url;
        
        return $this;
    }
    
    public function getLang(): ?string
    {
        return $this->lang;
    }
    
    public function setLang(?string $lang): self
    {
        $this->lang = $lang;
        
        return $this;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        
        return $this;
    }
    
    public function getImg(): ?string
    {
        return $this->img;
    }
    
    public function setImg(?string $img): self
    {
        $this->img = $img;
        
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
