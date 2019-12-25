<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/public/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Factory\Pvp;

use Interop\Container\ContainerInterface;
use Lotgd\Core\Pvp\Listing as PvpListing;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Listing implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $listing = new PvpListing();
        $listing->setContainer($container);

        return $listing;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
