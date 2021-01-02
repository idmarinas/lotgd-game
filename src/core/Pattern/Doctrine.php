<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Pattern;

use Doctrine\ORM\EntityManager;

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
