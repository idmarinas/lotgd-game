<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Doctrine\ORM\EntityRepository as DoctrineRepository;
use Tracy\Debugger;

class SettingsRepository extends DoctrineRepository
{
    /**
     * Get a installed version, this avoid cache.
     *
     * @return string
     */
    public function getInstalledVersion(): string
    {
        try
        {
            $query = $this->_em->createQuery("SELECT u.value FROM $this->_entityName u WHERE u.setting = 'installer_version'");

            return $query->getSingleScalarResult();
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return '';
        }
    }

    /**
     * Set installed version.
     *
     * @return self
     */
    public function setInstalledVersion(string $version): self
    {
        try
        {
            $query = $this->_em->createQuery("UPDATE $this->_entityName u SET u.value = ?1 WHERE u.setting = 'installer_version'");

            $query->setParameter(1, $version)
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);
        }

        return $this;
    }
}
