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

namespace Lotgd\Core\Db\Pattern;

use Zend\Db\Metadata\Metadata;

/**
 * contain all function to manage prefix of tables.
 */
trait DbTool
{
    protected $connection = null;

    /**
     * Check name of data base.
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->getAdapter()->getPlatform()->getName();
    }

    /**
     * Check connection to DB.
     *
     * @return bool
     */
    public function connect(): bool
    {
        if (null === $this->connection)
        {
            try
            {
                //-- Execute a simple query for test connection
                $metadata = new Metadata($this->getAdapter());
                $metadata->getTableNames();

                $this->connection = true;
            }
            catch (\Throwable $ex)
            {
                $this->errorInfo = $ex->getMessage();

                $this->connection = false;
            }
        }

        return $this->connection;
    }
}
