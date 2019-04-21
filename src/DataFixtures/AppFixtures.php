<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use function rand;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    /**
     * AppFixtures constructor.
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
        $this->loadBlogPost($manager);
        $this->loadComments($manager);

    }

    public function loadBlogPost(ObjectManager $manager)
    {
        $user = $this->getReference('admin_user');
        for($i=0;$i<100;$i++){
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setContent($this->faker->realText(200));
            $blogPost->setSlug("first-post");
            $blogPost->setPublished($this->faker->dateTime);
            $blogPost->setAuthor($user);
            $this->setReference("blog_post_$i",$blogPost);
            $manager->persist($blogPost);
        }
        $manager->flush();

    }

    public function loadComments(ObjectManager $manager){
        for ($i=0;$i<100;$i++){
            for($j=0;$j < rand(1,10);$j++){
                $comment = new Comment();
                $comment->setContent($this->faker->realText(100));
                $comment->setPublished($this->faker->dateTime);
                $comment->setAuthor($this->getReference('admin_user'));
                $comment->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
            }
        }
        $manager->flush();

    }
    public function  loadUser(ObjectManager $manager){
        $user = new User();
        $user->setUsername("admin");
        $user->setEmail("admin@blog.com");
        $user->setFullname("Volkan Oluç");
        $user->setPassword($this->passwordEncoder->encodePassword($user,"123456a"));
        $this->addReference('admin_user',$user);
        $manager->persist($user);
        $manager->flush();
    }
}
