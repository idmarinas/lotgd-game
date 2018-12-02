<?php

/**
 * This trait contain all function to manage prefix of tables.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Lib\Pattern;

trait DbTool
{
    /**
     * Check name of data base.
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->getAdapter()->getPlatform()->getName();
    }
}
