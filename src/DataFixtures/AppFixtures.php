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

    private const USERS = [
      ['username' => "admin2", 'email' => "admin2@gmail.com", 'name'=> 'Volkan Admin','password'=> "123456!"],
        ['username' => "admin3", 'email' => "admin3@gmail.com", 'name'=> 'Admin3','password'=> "123456!"],
        ['username' => "admin4", 'email' => "admin4@gmail.com", 'name'=> 'Admin4','password'=> "123456!"],
        ['username' => "admin5", 'email' => "admin5@gmail.com", 'name'=> 'Admin5','password'=> "123456!"]
    ];
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
        for($i=0;$i<100;$i++){
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setContent($this->faker->realText(200));
            $blogPost->setSlug("first-post");
            $blogPost->setPublished($this->faker->dateTime);
            $authorReference = $this->getRandomUserReference();
            $blogPost->setAuthor($authorReference);
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
                $authorReference = $this->getRandomUserReference();
                $comment->setAuthor($authorReference);
                $comment->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
            }
        }
        $manager->flush();

    }
    public function  loadUser(ObjectManager $manager){
        foreach (self::USERS as $USER){
            $user = new User();
            $user->setUsername($USER['username']);
            $user->setEmail($USER['email']);
            $user->setFullname($USER['name']);
            $user->setPassword($this->passwordEncoder->encodePassword($user,$USER['password']));
            $this->addReference('user_'.$USER['username'],$user);
            $manager->persist($user);
            $manager->flush();
        }
    }

    /**
     * @return User
     */
    protected function  getRandomUserReference(): User
    {
        return $this->getReference('user_'.self::USERS[rand(0,3)]['username']);
    }
}
