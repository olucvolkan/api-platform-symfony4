<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     itemOperations={
 *     "get"={
 *          "normalization_context"={
 *           "groups"= {"get-blog-post-with-author"}
 *          }
 *
 *     },
 *     "put" = {"access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object.getAuthor() == user"}
 * },
 *     collectionOperations={"get",
 *     "post" = {
 *     "access_control"="is_granted('IS_AUTHENTICATED_FULLY')"
 *        }
 *     }
 *
 * )
 * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
 */
class BlogPost implements AuthoredEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-blog-post-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Groups({"post","get-blog-post-with-author"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $published;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(min="5",max="500")
     * @Groups({"post","get-blog-post-with-author"})
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User",inversedBy="post")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post","get-blog-post-with-author"})
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Groups({"post","get-blog-post-with-author"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment",mappedBy="blogPost")
     * @ApiSubresource()
     * @Groups({"get-blog-post-with-author"})
     */
    private $comment;

    public function __construct()
    {
        $this->comment = new ArrayCollection();
        $this->published = new \DateTime("now");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished()
    {
        $this->published = new \DateTime("now");

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

    /**
     * @return User
     */
    public function getAuthor():User
    {
        return $this->author;
    }

    /**
     * @param UserInterface $user
     * @return AuthoredEntityInterface
     */
    public function setAuthor(UserInterface $user): AuthoredEntityInterface
    {
        $this->author = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }





}
