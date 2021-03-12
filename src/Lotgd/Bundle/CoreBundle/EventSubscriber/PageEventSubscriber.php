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

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class PageEventSubscriber implements EventSubscriberInterface
{
    protected $params;
    protected $request;
    protected $environment;

    public function __construct(ParameterBagInterface $params, RequestStack $request, Environment $environment)
    {
        $this->params      = $params;
        $this->request     = $request->getCurrentRequest();
        $this->environment = $environment;
    }

    public function onKernelRequest()
    {
        $controller = \preg_replace('/::.+/', '', $this->request->get('_controller'));

        //-- Define Twig global variable for translator_domain
        $textDomain = \defined($controller.'::TRANSLATOR_DOMAIN') ? \constant($controller.'::TRANSLATOR_DOMAIN') : null;
        $this->environment->addGlobal('translator_domain', $textDomain);

        //-- Define Twig global variable for render menu
        $menu = \defined($controller.'::LOTGD_MENU') ? \constant($controller.'::LOTGD_MENU') : 'lotgd_core.menu';
        $this->environment->addGlobal('lotgd_menu', $menu);
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
