<?php

namespace App\Entity;

use App\Service\Like\LikeableInterface;
use App\Service\Like\Traits\LikeDislikeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article implements LikeableInterface
{
    const ITEMS = 10;

    use TimestampableEntity, LikeDislikeTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="article", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookmarkArticle", mappedBy="article", orphanRemoval=true)
     */
    private $bookmarkArticles;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="articles")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="articles")
     */
    private $category;

    /**
     * @ORM\Column(type="integer")
     */
    private $commentCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $bookmarkCount = 0;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->bookmarkArticles = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

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
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    public static function getLikeClass()
    {
        return LikeArticle::class;
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
            $bookmarkArticle->setArticle($this);
        }

        return $this;
    }

    public function removeBookmarkArticle(BookmarkArticle $bookmarkArticle): self
    {
        if ($this->bookmarkArticles->contains($bookmarkArticle)) {
            $this->bookmarkArticles->removeElement($bookmarkArticle);
            // set the owning side to null (unless already changed)
            if ($bookmarkArticle->getArticle() === $this) {
                $bookmarkArticle->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getCommentCount(): ?int
    {
        return $this->commentCount;
    }

    public function incCommentCount(): self
    {
        $this->commentCount++;

        return $this;
    }

    public function getBookmarkCount(): ?int
    {
        return $this->bookmarkCount;
    }

    public function incBookmarkCount(): self
    {
        $this->bookmarkCount++;

        return $this;
    }
}
