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

namespace Lotgd\Bundle\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class PageEventSubscriber implements EventSubscriberInterface
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $controller = \preg_replace('/::.+/', '', $event->getRequest()->get('_controller'));

        //-- Define Twig global variable for translator_domain
        $textDomain = \defined($controller.'::TRANSLATOR_DOMAIN') ? \constant($controller.'::TRANSLATOR_DOMAIN') : null;
        $this->twig->addGlobal('translator_domain', $textDomain);

        //-- Define Twig global variable for render menu
        $menu = \defined($controller.'::LOTGD_MENU') ? \constant($controller.'::LOTGD_MENU') : 'lotgd_bundle.menu';
        $this->twig->addGlobal('lotgd_menu', $menu);
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
