<?php

/**
 * This trait contain all function to manage prefix of tables.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Lib\Pattern;

use Zend\Db\Metadata\Metadata;

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
                $metadata = new Metadata(self::$wrapper->getAdapter());
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
