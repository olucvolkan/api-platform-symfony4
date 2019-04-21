<?php
/**
 * Created by PhpStorm.
 * User: volki
 * Date: 22.04.2019
 * Time: 00:57
 */

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordHashSubscriber implements EventSubscriberInterface
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->encoder = $passwordEncoder;
    }

    public static function getSubscribedEvents()
    {
       return [
           KernelEvents::VIEW => ['hashPassword',EventPriorities::PRE_WRITE]
       ];

    }

    public function hashPassword(GetResponseForControllerResultEvent $event){
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$user instanceof  User || Request::METHOD_POST !==$method){
            return ;

        }

        $user->setPassword(
            $this->encoder->encodePassword($user,$user->getPassword())
        );
    }
}