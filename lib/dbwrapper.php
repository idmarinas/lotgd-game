<?php
// addnews ready
// translator ready
// mail ready

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Profiler\Profiler;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata\Metadata;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Sql;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

require_once 'lib/errorhandling.php';
require_once 'lib/datacache.php';
require_once 'settings.php';

$dbinfo = [];
$dbinfo['queriesthishit'] = 0 ;
$dbinfo['querytime']=0;

Class DB
{
	private static $adapter;
	private static $generatedValue = null;
	private static $affectedRows = 0;
	private static $errorInfo = null;
	private static $sqlString = null;
	private static $sql = null;

	public static function setAdapter(Array $options, $force = false)
	{
        if (! isset($options['driver']) || $options['driver'] == '') $options['driver'] = 'Pdo_Mysql';

        if ('Pdo_Mysql' == $options['driver'])
        {
            $options['driver_options'] = [
                \PDO::MYSQL_ATTR_FOUND_ROWS => true
            ];
        }

		if (! self::$adapter || true === $force )
		{
			$adapter = new Adapter($options);

			self::$adapter = $adapter->setProfiler(new Profiler());
		}
	}

	public static function getAdapter()
	{
		if (! self::$adapter)
		{
			$title = 'Error in the database';
			$message = 'There was an error connecting to the game database. <br> Please wait a few minutes, if the problem persists contact the administrators.';

			die(self::template($title, $message));
		}
		return self::$adapter;
	}

	public static function connect()
	{
		try
		{
			//-- Execute a simple query for test connection
			$metadata = new Zend\Db\Metadata\Metadata(self::getAdapter());
			$metadata->getTableNames();

			return true;
		}
		catch(\Exception $ex)
		{
			self::$errorInfo = $ex->getMessage();

			return false;
		}
	}

    /**
     * Prefix for tables
     *
     * @param string|array $tablename Name of table
     * @param false|string $force If you want to force a prefix
     *
     * @return string|array
     */
	public static function prefix($tablename, $force = false)
	{
		global $DB_PREFIX;

		if ($force === false)
		{
			// The following file should be used to override or modify the
			// special_prefixes array to be correct for your site.  Do NOT
			// do this unles you know EXACTLY what this means to you, your
			// game, your county, your state, your nation, your planet and
			// your universe!
			// Example: you change name of a table
			if (file_exists('prefixes.php')) $special_prefixes = include_once 'prefixes.php';

			$prefix = $DB_PREFIX;
			if (isset($special_prefixes[$tablename])) $prefix = $special_prefixes[$tablename];
		}
		else return $prefix = $force;

		if (is_array($tablename))
        {
            list($key, $value) = each($tablename);

            return [$key => $prefix . $value];
        }
        else return $prefix . $tablename;
	}

	/**
	 * Execute a query
	 *
	 * @param string $sql
	 *
	 * @return ResultSet
	 */
	public static function query($sql)
	{
		if (defined('DB_NODB') && ! defined('LINK')) return [];

		global $session, $dbinfo;

		$adapter = self::getAdapter();

		try
		{
			$adapter->getProfiler()->profilerStart($sql);
			$statement = $adapter->query($sql);
			$adapter->getProfiler()->profilerFinish();

			$result = $statement->execute();
		}
		catch(\Exception $ex)
		{
			self::$errorInfo = $ex->getMessage();

            $resultSet = new ResultSet;

		    return $resultSet->initialize([]);
		}

		$profiler = $adapter->getProfiler()->getLastProfile();

		if ($profiler['elapse'] >= 0.5)
			debug(sprintf('Slow Query (%ss): %s',
				round($profiler['elapse'],3),
				HTMLEntities($statement->getSql(), ENT_COMPAT, getsetting('charset', 'UTF-8'))
			));

		$dbinfo['queriesthishit']++;
		$dbinfo['querytime'] += $profiler['elapse'];

		//-- Save data for usage
		self::$generatedValue = $result->getGeneratedValue();
		self::$affectedRows = $result->getAffectedRows();
		self::$errorInfo = 	$result->getResource()->errorInfo();
        self::$sqlString = $statement->getSql();

		return $result;
	}

	public static function fetch_assoc(&$result)
	{
		if (is_array($result))
		{
			//cached data
			if (list($key, $val) = each($result)) return $val;
			else return false;
		}
		else if ('object' == gettype($result)) return $result->next();
		else $result;
	}

	public static function num_rows($result)
	{
		if (is_array($result)) return count($result);
		else if ('object' == gettype($result)) return $result->count();
		else return (int) $result;
	}

	public static function affected_rows($result = false)
	{
		if (false === $result) return self::$affectedRows;
		else return $result->getAffectedRows();
	}

	public static function insert_id()
	{
		return self::$generatedValue;
	}

	public static function free_result($result)
	{
		unset($result);
	}

	//& at the start returns a reference to the data array.
	//since it's possible this array is large, we'll save ourselves
	//the overhead of duplicating the array, then destroying the old
	//one by returning a reference instead.
	public static function &query_cached($sql, $name, $duration = 900)
	{
		//this function takes advantage of the data caching library to make
		//all of the other db_functions act just like MySQL queries but rely
		//instead on disk cached data.
		//if (getsetting("usedatacache", 0) == 1) debug("DataCache: $name");
		//standard is 15 minutes, als hooks don't need to be cached *that* often, normally you invalidate the cache properly

		$data = datacache($name,$duration);

		if (is_array($data))
		{
			reset($data);
			self::$affectedRows = -1;

			return $data;
		}
		else if ('object' == gettype($sql))
		{
			self::$sqlString = self::sql()->buildSqlString($sql);

			$result = self::query(self::$sqlString);
			if (false === $result) $data = [];
			else $data = self::toArray($result);
			updatedatacache($name, $data);
			reset($data);

			return $data;
		}
		else
		{
			$result = self::query($sql);
			if (false === $result) $data = [];
			else $data = self::toArray($result);
			updatedatacache($name, $data);
			reset($data);

			return $data;
		}
	}

	//-- Obtener el error de la conexión
	public static function error($result = false)
	{
		if (false !== $result) $r = $result->getResource()->errorInfo();
		else $r = self::$errorInfo;

		return $r;
	}

    /**
     * Check if table exist
     *
     * @param string $tablename
     *
     * @return false
     */
	public static function table_exists($tablename)
	{
		if (defined('DB_NODB') && !defined('LINK')) return false;

		$metadata = new Metadata(self::getAdapter());

		try
		{
            $table = $metadata->getTable($tablename);

			return true;
		}
		catch(Exception $e)
		{
			return false;
		}
	}

	//-- Comprobar la versión del servidor base de datos
	public static function get_server_version()
	{
		return self::getAdapter()->getPlatform()->getName();
	}

    /**
     * Quote value for safe using in DB
     *
     * @param string $value
     *
     * @return string
     */
	public static function quoteValue($value)
	{
		return (string) self::getAdapter()->getPlatform()->quoteValue((string) $value);
    }

	/**
     * Function to create template and show page on conexion die
     *
     * @param string $title
     * @param string $message
     * @param boolean $showtrace
     *
     * @return string
     */
	private static function template($title, $message, $showtrace = false)
	{
		require_once 'lib/sanitize.php';
		require_once 'lib/nltoappon.php';
		require_once 'lib/show_backtrace.php';

		$file = file_get_contents('error_docs/dberror.html');
		$message = full_sanitize(str_replace("`n", "<br />", nltoappon($message)));
		if ($showtrace) $message .= show_backtrace();

		return str_replace(array("{subject}", "{message}"), array($title, $message), $file);
	}

	/**
	 * Alias for funtions of Zend Framework (Component Zend DB)
	 */

	//-- Funciones de base de datos
	private static function sql()
	{
		if (! self::$sql)
		{
			$adapter = self::getAdapter();

			self::$sql = new Sql($adapter);
		}

		return self::$sql;
	}

    /**
     * Select API
     *
     * @param string|false $table
     * @param boolean $prefixed
     *
     * @return Object
     */
	public static function select($table = false, $prefixed = true)
	{
		if (defined('DB_NODB') && ! defined('LINK')) return false;

        if ($table && $prefixed) return self::sql()->select(DB::prefix($table));
        elseif ($table && ! $prefixed) return self::sql()->select($table);
		else return self::sql()->select();
	}

    /**
     * Insert API
     *
     * @param string|false $table
     * @param boolean $prefixed
     *
     * @return Object
     */
	public static function insert($table = false, $prefixed = true)
	{
		if (defined('DB_NODB') && ! defined('LINK')) return false;

		if ($table && $prefixed) return self::sql()->insert(DB::prefix($table));
        elseif ($table && ! $prefixed) return self::sql()->select($table);
		else return self::sql()->insert();
    }

    /**
     * Update API
     *
     * @param string|false $table
     * @param boolean $prefixed
     *
     * @return Object
     */
	public static function update($table = false, $prefixed = true)
	{
		if (defined('DB_NODB') && ! defined('LINK')) return false;

		if ($table && $prefixed) return self::sql()->update(DB::prefix($table));
        elseif ($table && ! $prefixed) return self::sql()->select($table);
		else return self::sql()->update();
	}

    /**
     * Delete API
     *
     * @param string|false $table
     * @param boolean $prefixed
     *
     * @return Object
     */
	public static function delete($table = false, $prefixed = true)
	{
		if (defined('DB_NODB') && ! defined('LINK')) return false;

		if ($table && $prefixed) return self::sql()->delete(DB::prefix($table));
        elseif ($table && ! $prefixed) return self::sql()->select($table);
		else return self::sql()->delete();
	}

    /**
     * Execute a object type SQL
     *
     * @param object $object
     *
     * @return ResultSet
     */
	public static function execute($object)
	{
		if ('object' != gettype($object)) return false;

		$objectString = self::sql()->buildSqlString($object);
        self::$sqlString = $objectString;

		return self::query($objectString);
	}

	/**
	 * Create Zend\Db\Sql\Predicate\Expression for uses in Zend DB
	 *
	 * @param string $expresion
	 *
	 * @return string
	 */
	public static function expression($expresion = null)
	{
		if (is_string($expresion))
		{
			return new Expression($expresion);
		}

		return;
	}

    /**
     * Get a sql query string
     *
     * @return string
     */
    public static function sqlString()
    {
        return self::$sqlString;
    }

    //-- Funciones para paginación
    /**
     * Undocumented function
     *
     * @param Select $select
     * @param integer $page
     * @param integer $perpage
     *
     * @return Object|Paginator
     */
	public static function paginator($select, $page = 1, $perpage = 25)
	{
        //-- Se combierte $page en un número y si es 0 se pone como 1
        $page = max(1, (int) $page);

		$paginatorAdapter = new DbSelect($select, self::getAdapter());
        $paginator = new Paginator($paginatorAdapter);
        // Página actual
        $paginator->setCurrentPageNumber($page);
        // Número máximo de resultados por página
        $paginator->setItemCountPerPage($perpage);

        self::$sqlString = self::sql()->buildSqlString($select);

        return $paginator;
	}

    /**
     * Navigation menu used with Paginator
     *
     * @param Zend\Paginator\Paginator $paginator
     * @param string $url
     * @param boolean $forcePages Force to show pages if only have 1 page
     *
     * @return void
     */
    public static function pagination($paginator, $url, $forcePages = false)
    {
        if ($paginator instanceof Paginator) $paginator = $paginator->getPages('all');

        if (1 >= $paginator->pageCount && ! $forcePages) return;

        addnav('Pages');
        $union = strpos($url, '?') === false ? '?' : '&';
        foreach($paginator->pagesInRange as $key => $page)
        {
            $minpage = (($page - 1) * $paginator->itemCountPerPage) + 1;
            $maxpage = $paginator->itemCountPerPage * $page;
            $maxpage = ($paginator->totalItemCount >= $maxpage? $maxpage : $paginator->totalItemCount);

            $text = ($page != $paginator->current ? 'Page %s (%s-%s)' : '`b`#Page %s (%s-%s)`0`b');
            addnav([$text, $page, $minpage, $maxpage], "$url{$union}page=$page");
        }
    }

	/**
	 * Get an array of result of DB::query
	 *
	 * @return array
	 */
	public static function toArray($result)
	{
		$resultSet = new ResultSet;

		return $resultSet->initialize($result)->toArray();
	}
}

/**
 * Funciones legado, para que funcionen en todos los módulos y partes del juego
 */
function db_prefix($tablename, $force = false)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0 please use "%s" instead',
        __METHOD__,
		'DB::prefix'
    ), E_USER_DEPRECATED);

	return DB::prefix($tablename, $force = false);
}
function db_query($sql, $die = true)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0 please use "%s" instead',
        __METHOD__,
		'DB::query'
    ), E_USER_DEPRECATED);

	return DB::query($sql, $die = true);
}
function db_fetch_assoc(&$result)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 2.0.0 no need use this function. Please read documentation of Zend\\Db for see how use this component. Link %s',
        __METHOD__,
		'https://docs.zendframework.com/zend-db/'
    ), E_USER_DEPRECATED);

	return DB::fetch_assoc($result);
}
function db_num_rows($result)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0 please use "%s" instead. Please read documentation of Zend\\Db for see how use this component. Link %s',
        __METHOD__,
		'DB::num_rows',
		'https://docs.zendframework.com/zend-db/'
    ), E_USER_DEPRECATED);

	return DB::num_rows($result);
}
function db_affected_rows($link = false)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0 please use "%s" instead. Please read documentation of Zend\\Db for see how use this component. Link %s',
        __METHOD__,
		'$result->getAffectedRows()',
		'https://docs.zendframework.com/zend-db/'
    ), E_USER_DEPRECATED);

	return DB::affected_rows($link);
}
function db_free_result($result)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0 please use "%s" instead',
        __METHOD__,
		'DB::free_result'
    ), E_USER_DEPRECATED);

	return DB::free_result($result);
}
function &db_query_cached($sql, $name, $duration = 900)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0 please use "%s" instead',
        __METHOD__,
		'DB::query_cached'
    ), E_USER_DEPRECATED);

	return DB::query_cached($sql, $name, $duration = 900);
}
function db_insert_id()
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0 please use "%s" instead',
        __METHOD__,
		'DB::insert_id'
    ), E_USER_DEPRECATED);

	return DB::insert_id();
}
function db_error($link = false)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0 please use "%s" instead',
        __METHOD__,
		'DB::error'
    ), E_USER_DEPRECATED);

	return DB::error($link);
}
function db_table_exists($tablename)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0 please use "%s" instead',
        __METHOD__,
		'DB::table_exists'
    ), E_USER_DEPRECATED);

	return DB::table_exists($tablename);
}
function db_get_server_version()
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0 please use "%s" instead',
        __METHOD__,
		'DB::get_server_version'
    ), E_USER_DEPRECATED);

	return DB::get_server_version();
}
