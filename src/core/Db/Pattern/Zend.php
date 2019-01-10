<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 3.1.0
 */

namespace Lotgd\Core\Db\Pattern;

use Zend\Db\Sql\{
    Delete,
    Insert,
    Predicate\Expression,
    Select,
    Sql,
    Update
};
use Zend\Paginator\{
    Adapter\DbSelect as DbSelectPaginator,
    Paginator
};

/**
 * Contain all methods for zend-db component.
 */
trait Zend
{
    protected $sql = null;

    /**
     * Select API.
     *
     * @param string|array|TableIdentifier|null $table
     * @param bool                              $prefixed
     *
     * @return object
     */
    public function select($table = null, $prefixed = null)
    {
        $table = $prefixed ? $this->prefix($table) : $table;

        if ($table)
        {
            return new Select($table);
        }

        return new Select();
    }

    /**
     * Insert API.
     *
     * @param string|TableIdentifier|null $table
     * @param bool                        $prefixed
     *
     * @return object
     */
    public function insert($table = null, $prefixed = null)
    {
        $table = $prefixed ? $this->prefix($table) : $table;

        if ($table)
        {
            return new Insert($table);
        }

        return new Insert();
    }

    /**
     * Update API.
     *
     * @param string|TableIdentifier|null $table
     * @param bool                        $prefixed
     *
     * @return object
     */
    public function update($table = null, $prefixed = null)
    {
        $table = $prefixed ? $this->prefix($table) : $table;

        if ($table)
        {
            return new Update($table);
        }

        return new Update();
    }

    /**
     * Delete API.
     *
     * @param string|TableIdentifier|null $table
     * @param bool                        $prefixed
     *
     * @return object
     */
    public function delete($table = null, $prefixed = null)
    {
        $table = $prefixed ? $this->prefix($table) : $table;

        if ($table)
        {
            return new Delete($table);
        }

        return new Delete();
    }

    /**
     * Execute a object type SQL.
     *
     * @param object $object
     *
     * @return ResultSet
     */
    public function execute($object)
    {
        if ('object' != gettype($object))
        {
            return false;
        }

        return $this->query($this->sql()->buildSqlString($object));
    }

    /**
     * Generate a paginator query.
     *
     * @param Select $select
     * @param int    $page
     * @param int    $perpage
     *
     * @return object|Paginator
     */
    public function paginator($select, int $page = 1, int $perpage = 25)
    {
        $page = max(1, $page);

        $paginatorAdapter = new DbSelectPaginator($select, $this->getAdapter());
        $paginator = new Paginator($paginatorAdapter);
        // Curren page
        $paginator->setCurrentPageNumber($page);
        // Max number of results per pag
        $paginator->setItemCountPerPage($perpage);

        $this->sqlString = $this->sql()->buildSqlString($select);
        $this->queriesthishit += 2;

        return $paginator;
    }

    /**
     * Create Zend\Db\Sql\Predicate\Expression for uses in Zend DB.
     *
     * @param string $expresion
     */
    public function expression(string $expresion = null)
    {
        if (is_string($expresion))
        {
            return new Expression($expresion);
        }

        return;
    }

    /**
     * Quote value for safe using in DB.
     *
     * @param string $value
     *
     * @return string
     */
    public function quoteValue($value): string
    {
        //-- Not do if not exist connection to DB
        if (false === $this->connect())
        {
            return $value;
        }

        return $this->getAdapter()->getPlatform()->quoteValue($value);
    }

    public function sql()
    {
        if (! $this->sql)
        {
            $this->sql = new Sql($this->getAdapter());
        }

        return $this->sql;
    }
}
