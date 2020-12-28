<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 2.0.0
 */

namespace Lotgd\Core\Fixed;

use Laminas\Db\Metadata\Metadata;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Paginator\Paginator;
use Lotgd\Core\Db\Dbwrapper as CoreDbwrapper;

\trigger_error(\sprintf(
    'Class %s is deprecated, please use Doctrine instead. "$doctrine = LotgdKernel::get("doctrine.orm.entity_manager")"',
    Dbwrapper::class
), E_USER_DEPRECATED);

/**
 * Static class to access a basic functions of DB.
 *
 * @deprecated 4.4.0
 */
class Dbwrapper
{
    /**
     * Instance of Dbwrapper.
     *
     * @var Lotgd\Core\Db\CoreDbwrapper
     */
    private static $wrapper;

    /**
     * Execute a query.
     *
     * @param string $sql
     *
     * @return ResultSet
     */
    public static function query($sql)
    {
        if (\defined('DB_NODB') && ! \defined('LINK'))
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
     * @param string|array|TableIdentifier|null $table
     * @param bool                              $prefixed
     *
     * @return object
     */
    public static function select($table = null, $prefixed = null)
    {
        if (\defined('DB_NODB') && ! \defined('LINK'))
        {
            return false;
        }

        return self::$wrapper->select($table, $prefixed);
    }

    /**
     * Insert API.
     *
     * @param string|TableIdentifier|null $table
     * @param bool                        $prefixed
     *
     * @return object
     */
    public static function insert($table = null, $prefixed = null)
    {
        if (\defined('DB_NODB') && ! \defined('LINK'))
        {
            return false;
        }

        return self::$wrapper->insert($table, $prefixed);
    }

    /**
     * Update API.
     *
     * @param string|TableIdentifier|null $table
     * @param bool                        $prefixed
     *
     * @return object
     */
    public static function update($table = null, $prefixed = null)
    {
        if (\defined('DB_NODB') && ! \defined('LINK'))
        {
            return false;
        }

        return self::$wrapper->update($table, $prefixed);
    }

    /**
     * Delete API.
     *
     * @param string|TableIdentifier|null $table
     * @param bool                        $prefixed
     *
     * @return object
     */
    public static function delete($table = null, ?bool $prefixed = null)
    {
        if (\defined('DB_NODB') && ! \defined('LINK'))
        {
            return false;
        }

        return self::$wrapper->delete($table, $prefixed);
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
     *
     * @return Laminas\Paginator\Paginator
     */
    public static function paginator($select, int $page = 1, int $perpage = 25)
    {
        return self::$wrapper->paginator($select, $page, $perpage);
    }

    /**
     * Navigation menu used with Paginator.
     *
     * @param Laminas\Paginator\Paginator $paginator
     * @param bool|null                   $forcePages Force to show pages if only have 1 page
     */
    public static function pagination(Paginator $paginator, string $url, $forcePages = null)
    {
        $paginator = $paginator->getPages('all');

        if (1 >= $paginator->pageCount && ! $forcePages)
        {
            return;
        }

        $union = false === \strpos($url, '?') ? '?' : '&';
        \LotgdNavigation::addHeader('common.pagination.title');

        foreach ($paginator->pagesInRange as $page)
        {
            $minItem = (($page - 1) * $paginator->itemCountPerPage) + 1;
            $maxItem = \min($paginator->itemCountPerPage * $page, $paginator->totalItemCount);

            $text = ($page != $paginator->current ? 'common.pagination.page' : 'common.pagination.current');
            \LotgdNavigation::addNav($text, "{$url}{$union}page={$page}", [
                'params' => [
                    'page'  => $page,
                    'item'  => $minItem,
                    'total' => $maxItem,
                ],
            ]);
        }
    }

    /**
     * Create Laminas\Db\Sql\Predicate\Expression for uses in Zend DB.
     *
     * @param string $expresion
     *
     * @return string
     */
    public static function expression(?string $expresion = null)
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
     * @param mixed $result
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
     */
    public static function table_exists(string $tablename): bool
    {
        if (\defined('DB_NODB') && ! \defined('LINK'))
        {
            return false;
        }

        try
        {
            $metadata = new Metadata(self::$wrapper->getAdapter());
            $table    = $metadata->getTable($tablename);

            return true;
        }
        catch (\Throwable $e)
        {
            return false;
        }
    }

    /**
     * Quote value for safe using in DB.
     *
     * @param string $value
     */
    public static function quoteValue($value): string
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
     */
    public static function connect(): bool
    {
        return self::$wrapper->connect();
    }

    /**
     * Add wrapper to script.
     *
     * @param Lotgd\Core\Db\CoreDbwrapper $wrapper
     */
    public static function wrapper(CoreDbwrapper $wrapper)
    {
        self::$wrapper = $wrapper;
    }
}

\class_alias('Lotgd\Core\Fixed\Dbwrapper', 'DB', false);
