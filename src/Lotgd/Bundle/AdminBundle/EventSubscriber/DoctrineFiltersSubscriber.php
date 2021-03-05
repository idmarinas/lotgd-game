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
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
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
            KernelEvents::CONTROLLER => 'disableDoctrineFilterOnAdmin',
        ];
    }

    public function disableDoctrineFilterOnAdmin(ControllerEvent $event)
    {
        $controller = is_array($event->getController()) ? $event->getController()[0] : $event->getController();
        if ($controller instanceof CRUDController)
        {
            //-- Disable filter "softdeleteable" for all admin controllers
            $this->em->getFilters()->disable('softdeleteable');
        }
    }
}
