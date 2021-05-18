<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\AvatarBundle\EventSubscriber;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\EventDispatcher\Event;

class CreateAvatarEventSubscriber extends Event implements EventSubscriberInterface
{
    private $router;
    private $security;

    public function __construct(UrlGeneratorInterface $router, Security $security)
    {
        $this->router      = $router;
        $this->security     = $security;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $route = $event->getRequest()->get('_route');
        /** @var \Lotgd\Bundle\UserBundle\Entity\User */
        $user = $this->security->getUser();

        if ($route != 'lotgd_avatar_create' && $user && ! count($user->getAvatars()))
        {
            $this->stopPropagation();
            $event->setResponse(new RedirectResponse($this->router->generate('lotgd_avatar_create')));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }
}
