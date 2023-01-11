<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $salt = null;

    #[ORM\Column]
    private ?bool $newsletter = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Annonce::class)]
    private Collection $annonces;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?UserAddress $address = null;

    #[ORM\OneToOne(mappedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\ManyToOne]
    private ?ProfilePicture $ProfilePicture = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: OldProfilePicture::class)]
    private Collection $oldProfilePictures;

    public function __construct()
    {
        $this->salt = crc32(uniqid('', true));
        $this->annonces = new ArrayCollection();
        $this->oldProfilePictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function isNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
            $annonce->setOwner($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getOwner() === $this) {
                $annonce->setOwner(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?UserAddress
    {
        return $this->address;
    }

    public function setAddress(?UserAddress $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): self
    {
        // set the owning side of the relation if necessary
        if ($cart->getOwner() !== $this) {
            $cart->setOwner($this);
        }

        $this->cart = $cart;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getProfilePicture(): ?ProfilePicture
    {
        return $this->ProfilePicture;
    }

    public function setProfilePicture(?ProfilePicture $ProfilePicture): self
    {
        $this->ProfilePicture = $ProfilePicture;

        return $this;
    }

    /**
     * @return Collection<int, OldProfilePicture>
     */
    public function getOldProfilePictures(): Collection
    {
        return $this->oldProfilePictures;
    }

    public function addOldProfilePicture(OldProfilePicture $oldProfilePicture): self
    {
        if (!$this->oldProfilePictures->contains($oldProfilePicture)) {
            $this->oldProfilePictures->add($oldProfilePicture);
            $oldProfilePicture->setOwner($this);
        }

        return $this;
    }

    public function removeOldProfilePicture(OldProfilePicture $oldProfilePicture): self
    {
        if ($this->oldProfilePictures->removeElement($oldProfilePicture)) {
            // set the owning side to null (unless already changed)
            if ($oldProfilePicture->getOwner() === $this) {
                $oldProfilePicture->setOwner(null);
            }
        }

        return $this;
    }
}
