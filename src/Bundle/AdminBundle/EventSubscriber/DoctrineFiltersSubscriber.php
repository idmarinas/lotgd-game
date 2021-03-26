<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\AdminBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DoctrineFiltersSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'disableDoctrineFilterOnAdmin',
        ];
    }

    public function disableDoctrineFilterOnAdmin(RequestEvent $event)
    {
        if ('security.firewall.map.context.admin' == $event->getRequest()->attributes->get('_firewall_context'))
        {
            //-- Disable filter "softdeleteable" for all admin controllers
            $this->em->getFilters()->disable('softdeleteable');
        }
    }
}
