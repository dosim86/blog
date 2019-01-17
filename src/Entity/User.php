<?php

namespace App\Entity;

use App\Lib\Helper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email", message="V_EMAIL_ALREADY_USED", groups={"register"})
 * @UniqueEntity("username", message="V_USERNAME_ALREADY_USED", groups={"register"})
 * @UniqueEntity("apiKey", message="V_APIKEY_ALREADY_USED", groups={"profile"})
 */
class User implements UserInterface
{
    const AUTHOR_ITEM = 10;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(groups={"register"})
     * @Assert\Email(groups={"register"})
     * @Groups({"public"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"public"})
     */
    private $firstname;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="author")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="owner")
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=10, groups={"profile"})
     */
    private $aboutMe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookmarkArticle", mappedBy="user", orphanRemoval=true)
     */
    private $bookmarkArticles;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="subscribs")
     * @ORM\JoinTable(name="follower_subscribe",
     *      joinColumns={@ORM\JoinColumn(name="subscribe_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")}
     *      )
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="followers")
     */
    private $subscribs;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDisabled = false;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"public"})
     */
    private $rank = 0;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank(groups={"register"})
     * @Assert\Type(type="alnum", message="The value can only contain English characters and digits", groups={"register"})
     * @Groups({"public", "frontend"})
     */
    private $username;

    /**
     * @Assert\NotBlank(groups={"register"})
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActivated = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $activateHash;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @ORM\Version
     * @Groups({"public"})
     */
    private $lastActivityAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"public"})
     */
    private $avatar = 'default.png';

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     * @Assert\NotBlank(groups={"profile"})
     * @Groups({"public", "frontend"})
     */
    private $apiKey;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->bookmarkArticles = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->subscribs = new ArrayCollection();
        $this->lastActivityAt = new \DateTime();
        $this->apiKey = Helper::generateToken();
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
    public function getUsername(): ?string
    {
        return $this->username;
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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return Collection|Article[]
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
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setOwner($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getOwner() === $this) {
                $comment->setOwner(null);
            }
        }

        return $this;
    }

    public function getAboutMe(): ?string
    {
        return $this->aboutMe;
    }

    public function setAboutMe(?string $aboutMe): self
    {
        $this->aboutMe = $aboutMe;

        return $this;
    }

    /**
     * @return Collection|BookmarkArticle[]
     */
    public function getBookmarkArticles(): Collection
    {
        return $this->bookmarkArticles;
    }

    public function addBookmarkArticle(BookmarkArticle $bookmarkArticle): self
    {
        if (!$this->bookmarkArticles->contains($bookmarkArticle)) {
            $this->bookmarkArticles[] = $bookmarkArticle;
            $bookmarkArticle->setUser($this);
        }

        return $this;
    }

    public function removeBookmarkArticle(BookmarkArticle $bookmarkArticle): self
    {
        if ($this->bookmarkArticles->contains($bookmarkArticle)) {
            $this->bookmarkArticles->removeElement($bookmarkArticle);
            // set the owning side to null (unless already changed)
            if ($bookmarkArticle->getUser() === $this) {
                $bookmarkArticle->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(self $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers[] = $follower;
        }

        return $this;
    }

    public function removeFollower(self $follower): self
    {
        if ($this->followers->contains($follower)) {
            $this->followers->removeElement($follower);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSubscribs(): Collection
    {
        return $this->subscribs;
    }

    public function addSubscrib(self $subscrib): self
    {
        if (!$this->subscribs->contains($subscrib)) {
            $this->subscribs[] = $subscrib;
            $subscrib->addFollower($this);
        }

        return $this;
    }

    public function removeSubscrib(self $subscrib): self
    {
        if ($this->subscribs->contains($subscrib)) {
            $this->subscribs->removeElement($subscrib);
            $subscrib->removeFollower($this);
        }

        return $this;
    }

    public function isDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    public function setDisabled(bool $isDisabled): self
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function isActivated(): bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    public function getActivateHash(): ?string
    {
        return $this->activateHash;
    }

    public function setActivateHash(string $activateHash): self
    {
        $this->activateHash = $activateHash;

        return $this;
    }

    public function getLastActivityAt(): ?\DateTimeInterface
    {
        return $this->lastActivityAt;
    }

    public function refreshLastActivity(): self
    {
        $this->lastActivityAt = new \DateTime();

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }
}
