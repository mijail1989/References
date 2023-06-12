<?php

namespace App\Entity;


use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\RegistrationController;

#[ApiResource(operations: [
    new Post(
        name: 'registration', 
        uriTemplate: '/registration', 
        controller: RegistrationController::class
    ),
    new Get(name: 'user_show', uriTemplate: 'public/user/{id}')
])]


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reference::class)]
    private Collection $refs;

    #[ORM\Column]
    private ?bool $isVerified = null;

    #[ORM\ManyToOne]
    private ?Skins $skin = null;


    public function __construct()
    {
        $this->refs = new ArrayCollection();
    }

    // public function jsonSerialize() {
    //     return [
    //         'id' => $this->id,
    //         'email' => $this->email,
    //         'roles' => $this->roles,
    //         'name' => $this->name,
    //         'phone' => $this->phone,
    //         'img' => $this->img,
    //         'refs'=>$this->refs
    //     ];
    // }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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

    /**
     * @return Collection<int, Reference>
     */
    public function getRefs(): Collection
    {
        return $this->refs;
    }

    public function addRef(Reference $ref): self
    {
        if (!$this->refs->contains($ref)) {
            $this->refs->add($ref);
            $ref->setUser($this);
        }

        return $this;
    }

    public function removeRef(Reference $ref): self
    {
        if ($this->refs->removeElement($ref)) {
            // set the owning side to null (unless already changed)
            if ($ref->getUser() === $this) {
                $ref->setUser(null);
            }
        }

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getSkin(): ?Skins
    {
        return $this->skin;
    }

    public function setSkin(?Skins $skin): self
    {
        $this->skin = $skin;

        return $this;
    }
}
