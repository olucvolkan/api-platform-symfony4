<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     itemOperations={
 *     "get",
 *      "put" = {"access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user"
 *      }
 *     },
 *     collectionOperations={
 *     "get",
 *     "post"={
 *          "access_control"="is_granted('IS_AUTHENTICATED_FULLY')"
 *      },
 *     "api_blog_posts_comments_get_subresource"= {
 *          "normalization_context"={
            "groups"= {"get-comment-with-author"}
 *          }
 *      }
 *     },
 *     denormalizationContext={
        "groups": {"post"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment implements AuthoredEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-comment-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","get-comment-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=5,max=300)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-comment-with-author"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User",inversedBy="comment")
     * @ORM\JoinColumn()
     * @Groups({"get-comment-with-author"})
     */
    private $author;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost",inversedBy="comment")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $blogPost;

    public function __construct()
    {
        $this->published = new \DateTime("now");
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     */
    public function setPublished(): void
    {
        $this->published = new \DateTime("now");
    }

    /**
     * @return User
     */
    public function getAuthor():User
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     */
    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBlogPost()
    {
        return $this->blogPost;
    }

    /**
     * @param mixed $blogPost
     */
    public function setBlogPost($blogPost): void
    {
        $this->blogPost = $blogPost;
    }

}
