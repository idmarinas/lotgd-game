<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\Doctrine\Persistance;

use \Doctrine\Persistence\AbstractManagerRegistry;

class ManagerRegistry extends AbstractManagerRegistry
{
    /**
     * @var array
     */
    protected $container = [];

    public function __construct($name, array $connections, array $managers, $defaultConnection, $defaultManager, $proxyInterfaceName)
    {
        $this->container = $managers;
        parent::__construct($name, $connections, array_keys($managers), $defaultConnection, $defaultManager, $proxyInterfaceName);
    }

    protected function getService($name)
    {
        return $this->container[$name];
       //alternatively supply the entity manager here instead
    }

    protected function resetService($name)
    {
        //don't want to lose the manager
    }

    public function getAliasNamespace($alias)
    {
        throw new \BadMethodCallException('Namespace aliases not supported');
    }
}
