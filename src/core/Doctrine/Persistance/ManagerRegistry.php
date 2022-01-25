<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\Doctrine\Persistance;

use BadMethodCallException;
use Doctrine\Persistence\AbstractManagerRegistry;

class ManagerRegistry extends AbstractManagerRegistry
{
    /**
     * @var array
     */
    protected $container = [];

    public function __construct($name, array $connections, array $managers, $defaultConnection, $defaultManager, $proxyInterfaceName)
    {
        $this->container = $managers;
        parent::__construct($name, $connections, \array_keys($managers), $defaultConnection, $defaultManager, $proxyInterfaceName);
    }

    /**
     * @return never
     */
    public function getAliasNamespace($alias): void
    {
        throw new BadMethodCallException('Namespace aliases not supported');
    }

    protected function getService($name)
    {
        return $this->container[$name];
        //alternatively supply the entity manager here instead
    }

    protected function resetService($name): void
    {
        //don't want to lose the manager
    }
}
