<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Factory\Output;

use Interop\Container\ContainerInterface;
use Lotgd\Core\Entity\Commentary as CommentaryEntity;
use Lotgd\Core\Output\Commentary as OutputCommentary;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Commentary implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $doctrine = $container->get(\Lotgd\Core\Db\Doctrine::class);

        $commentary = new OutputCommentary();
        $commentary->setDoctrine($doctrine)
            ->setEntity(new CommentaryEntity())
        ;

        return $commentary;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
