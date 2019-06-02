<?php
/**
 * Created by PhpStorm.
 * User: volki
 * Date: 24.04.2019
 * Time: 22:34
 */

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\BlogPost;
use App\Entity\Comment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthoredEntitySubscriber implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    public static function getSubscribedEvents()
    {
        return [KernelEvents::VIEW => ['getAuthenticatedUser', EventPriorities::PRE_WRITE]];
    }

    public function getAuthenticatedUser(GetResponseForControllerResultEvent $resultEvent)
    {
        $entity = $resultEvent->getControllerResult();
        $method = $resultEvent->getRequest()->getMethod();
        $author = $this->tokenStorage->getToken()->getUser();

        if((!$entity instanceof  BlogPost && !$entity instanceof Comment )||Request::METHOD_POST !== $method){
            return ;
        }

        $entity->setAuthor($author);



    }

}