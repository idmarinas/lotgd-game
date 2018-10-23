<?php

// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Lib\Dbwrapper;
use Zend\Db\Metadata\Metadata;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect as DbSelectPaginator;
use Zend\Paginator\Paginator;

class DB
{
    private static $wrapper;



    private static $generatedValue = null;
    private static $affectedRows = 0;
    private static $errorInfo = null;
    private static $sqlString = null;
    private static $sql = null;

    /**
     * Execute a query.
     *
     * @param string $sql
     *
     * @return ResultSet
     */
    public static function query($sql)
    {
        if (defined('DB_NODB') && ! defined('LINK'))
        {
            $resultSet = new ResultSet();

            return $resultSet->initialize([]);
        }

        return self::$wrapper->query($sql);
    }

    public static function affected_rows($result = false): int
    {
        return self::$wrapper->getAffectedRows($result);
    }

    public static function fetch_assoc(&$result)
    {
        return self::$wrapper->current($result);
    }

    public static function num_rows($result): int
    {
        return self::$wrapper->count($result);
    }

    /**
     * Select API.
     *
     * @param string|false $table
     * @param bool         $prefixed
     *
     * @return object
     */
    public static function select(string $table = '', bool $prefixed = true)
    {
        if (defined('DB_NODB') && ! defined('LINK'))
        {
            return false;
        }

        return self::$wrapper->select($table);
    }

    /**
     * Insert API.
     *
     * @param string $table
     * @param bool   $prefixed
     *
     * @return object
     */
    public static function insert(string $table = '', bool $prefixed = true)
    {
        if (defined('DB_NODB') && ! defined('LINK'))
        {
            return false;
        }

        return self::$wrapper->insert($table);
    }

    /**
     * Update API.
     *
     * @param string $table
     * @param bool   $prefixed
     *
     * @return object
     */
    public static function update(string $table = '', bool $prefixed = true)
    {
        if (defined('DB_NODB') && ! defined('LINK'))
        {
            return false;
        }

        return self::$wrapper->update($table);
    }

    /**
     * Delete API.
     *
     * @param string $table
     * @param bool   $prefixed
     *
     * @return object
     */
    public static function delete(string $table = '', bool $prefixed = true)
    {
        if (defined('DB_NODB') && ! defined('LINK'))
        {
            return false;
        }

        return self::$wrapper->delete($table);
    }

    /**
     * Execute a object type SQL.
     *
     * @param object $object
     *
     * @return ResultSet
     */
    public static function execute($object)
    {
        return self::$wrapper->execute($object);
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
    public static function paginator($select, int $page = 1, int $perpage = 25)
    {
        return self::$wrapper->paginator($select, $page, $perpage);
    }

    /**
     * Navigation menu used with Paginator.
     *
     * @param Zend\Paginator\Paginator $paginator
     * @param string                   $url
     * @param bool                     $forcePages Force to show pages if only have 1 page
     */
    public static function pagination($paginator, string $url, bool $forcePages = false)
    {
        if ($paginator instanceof Paginator)
        {
            $paginator = $paginator->getPages('all');
        }

        if (1 >= $paginator->pageCount && ! $forcePages)
        {
            return;
        }

        addnav('Pages');
        $union = false === strpos($url, '?') ? '?' : '&';

        foreach ($paginator->pagesInRange as $key => $page)
        {
            $minpage = (($page - 1) * $paginator->itemCountPerPage) + 1;
            $maxpage = $paginator->itemCountPerPage * $page;
            $maxpage = ($paginator->totalItemCount >= $maxpage ? $maxpage : $paginator->totalItemCount);

            $text = ($page != $paginator->current ? 'Page %s (%s-%s)' : '`b`#Page %s (%s-%s)`0`b');
            addnav([$text, $page, $minpage, $maxpage], "$url{$union}page=$page");
        }
    }

    /**
     * Create Zend\Db\Sql\Predicate\Expression for uses in Zend DB.
     *
     * @param string $expresion
     *
     * @return string
     */
    public static function expression(string $expresion = null)
    {
        return self::$wrapper->expression($expresion);
    }

    /**
     * Get a sql query string.
     *
     * @return string
     */
    public static function sqlString()
    {
        return self::$wrapper->getSql();
    }

    /**
     * Get an array of result of DB::query.
     *
     * @return array
     */
    public static function toArray($result): array
    {
        $resultSet = new ResultSet();

        return $resultSet->initialize($result)->toArray();
    }

    public static function insert_id()
    {
        return self::$wrapper->getGeneratedValue();
    }

    public static function free_result($result)
    {
        unset($result);
    }

    /**
     * Get error of connection.
     *
     * @param object|false $result
     */
    public static function error($result = false)
    {
        return self::$wrapper->errorInfo($result);
    }

    /**
     * Check if table exist.
     *
     * @param string $tablename
     *
     * @return bool
     */
    public static function table_exists(string $tablename): bool
    {
        if (defined('DB_NODB') && ! defined('LINK'))
        {
            return false;
        }

        try
        {
            $metadata = new Metadata(self::$wrapper->getAdapter());
            $table = $metadata->getTable($tablename);

            return true;
        }
        catch (\Throwable $e)
        {
            return false;
        }
    }

    /**
     * Check name of data base.
     *
     * @return string
     */
    public static function get_server_version()
    {
        return self::$wrapper->getAdapter()->getPlatform()->getName();
    }

    /**
     * Quote value for safe using in DB.
     *
     * @param string $value
     *
     * @return string
     */
    public static function quoteValue(string $value): string
    {
        return self::$wrapper->quoteValue($value);
    }

    /**
     * Prefix for tables.
     *
     * @param string|array $tablename Name of table
     * @param false|string $force     If you want to force a prefix
     *
     * @return string|array
     */
    public static function prefix($tablename, $force = false)
    {
        return self::$wrapper->prefix($tablename, $force);
    }

    /**
     * Check connection to DB.
     *
     * @return bool
     */
    public static function connect(): bool
    {
        try
        {
            //-- Execute a simple query for test connection
            $metadata = new Zend\Db\Metadata\Metadata(self::$wrapper->getAdapter());
            $metadata->getTableNames();

            return true;
        }
        catch (\Throwable $ex)
        {
            self::$errorInfo = $ex->getMessage();

            return false;
        }
    }

    /**
     * Add wrapper to script.
     *
     * @param Lotgd\Core\lib\Dbwrapper $wrapper
     */
    public static function wrapper(Dbwrapper $wrapper)
    {
        self::$wrapper = $wrapper;
    }


    /**
     * @deprecated
     */
    public static function query_cached($sql, $name, $duration = 900)
    {
        trigger_error(sprintf(
            'Usage of %s is obsolete since 3.0.0; and delete in version 3.1.0 please ovoid use "%s", use cache system to cache results',
            __METHOD__,
            __METHOD__
        ), E_USER_DEPRECATED);

        return self::query($sql);
    }
}
