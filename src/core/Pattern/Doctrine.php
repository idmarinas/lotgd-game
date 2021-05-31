<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Pattern;

use Doctrine\ORM\EntityManager;

@trigger_error(Doctrine::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
trait Doctrine
{
    /**
     * Doctrine instance.
     *
     * @var EntityManager
     */
    protected $doctrine;

    /**
     * Get repository.
     *
     * @param mixed $name
     */
    public function getDoctrineRepository($name): ?object
    {
        try
        {
            return $this->getDoctrine()->getRepository($name);
        }
        catch (\Throwable $th)
        {
            return null;
        }
    }

    /**
     * Get doctrine entity manager instance.
     */
    public function getDoctrine(): EntityManager
    {
        if ( ! $this->doctrine instanceof EntityManager)
        {
            $this->doctrine = $this->getService('doctrine.orm.entity_manager');
        }

        return $this->doctrine;
    }
}
