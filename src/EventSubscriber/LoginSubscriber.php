<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;

    }

    public function onLOgin()
    {

        $user = $this->security->getUser();
        $user->setLastLoginAT(new \DateTime());
        $this->entityManager->flush($user);
    }
      //https://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber
    public static function getSubscribedEvents(): array
    {

        return [
            LoginSuccessEvent::class => 'onLogin',
        ];
    }

}