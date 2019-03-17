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

namespace Lotgd\Core\Pattern;

use Doctrine\ORM\EntityManager;

trait Repository
{
    protected $doctrine;

    /**
     * Get repository.
     *
     * @return object|null
     */
    public function getDoctrineRepository($name)
    {
        if (! $this->doctrine instanceof EntityManager)
        {
            $this->doctrine = $this->getContainer(\Lotgd\Core\Db\Doctrine::class);
        }

        try
        {
            return $this->doctrine->getRepository($name);
        }
        catch (\Throwable $th)
        {
            return null;
        }
    }
}
