<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
#[UniqueEntity(fields: ['name'], message: 'There is already an account with this name')]
#[UniqueEntity(fields: ['noSIRET'], message: 'There is already an account with this noSIRET')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email(
        message: '{{ value }} n\'est pas un email valide .',
    )]
    #[Assert\NotBlank(
        message: 'L\'email est obligatoire .',
    )]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    #[Assert\Length(
        min: 6,
        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
        // max length allowed by Symfony for security reasons
        max: 4096,
    )]
    private $password;

    #[ORM\Column(type:"string", length:50, nullable:true, unique: true)]
    #[Assert\Length(
        min: 3,
        minMessage: 'Votre nom d\'utilisateur doit contenir au moins {{ limit }} caractères',
        max: 50,
        maxMessage: 'Votre nom d\'utilisateur doit contenir au plus {{ limit }} caractères',
    )]
    private $username;

    // Phone number (allow country code, spaces, dots, and dashes)
    #[ORM\Column(type:"string", length:255, nullable:true)]
    #[Assert\NotBlank(
        message: 'Le numéro de téléphone est obligatoire .',
    )]
    #[Assert\Regex(
        pattern: '/^(\+33|0)[1-9]([-. ]?[0-9]{2}){4}$/',
        message: 'Le numéro de téléphone n\'est pas valide .',
    )]
    private $phone;

    #[ORM\Column(type:"string", length:255, nullable:true, unique: true)]
    private $name;

    #[ORM\Column(type:"string", length:14, nullable:true, unique: true)]
    #[Assert\Regex(
        pattern: '/^[0-9]{14}$/',
        message: 'Le numéro SIRET n\'est pas valide. Il doit contenir 14 chiffres.',
    )]
    private $noSIRET;

    #[Vich\UploadableField(mapping: 'user_avatars', fileNameProperty: 'avatar')]
    private ?File $imageFile = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $avatar = null;

    #[ORM\Column(type:"datetime", nullable:true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Article::class, orphanRemoval: true)]
    private $articles;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'favoriteUsers')]
    private $favoriteArticles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Contact::class, orphanRemoval: true)]
    private $contacts;

    #[ORM\Column(type:"boolean")]
    private $isBlocked = false;

    #[ORM\Column]
    private bool $isVerified = false;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->favoriteArticles = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function __toString()
    {
        if ($this->name) {
            return '[PRO] '.$this->name;
        } elseif ($this->username) {
            return '[INDIV] '.$this->username;
        } else {
            return $this->id.' - '.$this->email;
        }
    }

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
        // add ROLE_BLOCKED if user is blocked
        if ($this->isBlocked) {
            $roles[] = 'ROLE_BLOCKED';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        $this->roles[] = $role;

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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNoSIRET(): ?int
    {
        return $this->noSIRET;
    }

    public function setNoSIRET(?int $noSIRET): self
    {
        $this->noSIRET = $noSIRET;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getFavoriteArticles(): Collection
    {
        return $this->favoriteArticles;
    }

    public function addFavoriteArticle(Article $favoriteArticle): self
    {
        if (!$this->favoriteArticles->contains($favoriteArticle)) {
            $this->favoriteArticles[] = $favoriteArticle;
            $favoriteArticle->addFavoriteUser($this);
        }

        return $this;
    }

    public function removeFavoriteArticle(Article $favoriteArticle): self
    {
        if ($this->favoriteArticles->removeElement($favoriteArticle)) {
            $favoriteArticle->removeFavoriteUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setUser($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getUser() === $this) {
                $contact->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getIsBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($serialized);
    }
}
