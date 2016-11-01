<?php
// addnews ready
// translator ready
// mail ready

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Profiler\Profiler;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata\Metadata;
use Zend\Db\Sql\Sql;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

require_once 'lib/errorhandling.php';
require_once 'lib/datacache.php';
require_once 'settings.php';

$dbinfo = [];
$dbinfo['queriesthishit']=0;
$dbinfo['querytime']=0;

Class DB
{
	private static $adapter;
	private static $generatedValue = null;
	private static $affectedRows = 0;
	private static $errorInfo = null;
	private static $sqlString = null;

	public static function setAdapter(Array $options, $force = false)
	{
		if (! isset($options['driver']) || $options['driver'] == '') $options['driver'] = 'Pdo_Mysql';

		if (! self::$adapter || true === $force )
		{
			$adapter = new Adapter($options);

			self::$adapter = $adapter->setProfiler(new Profiler());
		}
	}

	private static function getAdapter()
	{
		if (! self::$adapter)
		{
			$title = 'Error in the database';
			$message = 'Se ha producido un error al conectar a la base de datos del juego. <br>Por favor espere unos minutos, si el problema continua póngase en contacto con los administradores.';

			die(self::template($title, $message));
		}
		return self::$adapter;
	}

	public static function connect()
	{
		try
		{
			$connection = self::getAdapter()->getDriver()->getConnection();

			$connection->connect();
			$result = $connection->isConnected();
			$connection->disconnect();

			return $result;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	//-- Prefijos para las tablas, permite una clave, el nombre de la tabla correcto
	public static function prefix($tablename, $force = false)
	{
		global $DB_PREFIX;

		if ($force === false)
		{
			$special_prefixes = array();

			// The following file should be used to override or modify the
			// special_prefixes array to be correct for your site.  Do NOT
			// do this unles you know EXACTLY what this means to you, your
			// game, your county, your state, your nation, your planet and
			// your universe!
			if (file_exists("prefixes.php")) require_once("prefixes.php");

			$prefix = $DB_PREFIX;
			if (isset($special_prefixes[$tablename])) $prefix = $special_prefixes[$tablename];
		}
		else $prefix = $force;

		return $prefix . $tablename;
	}

	public static function query($sql, $die = true)
	{
		if (defined('DB_NODB') && ! defined('LINK')) return [];

		global $session, $dbinfo;

		$adapter = self::getAdapter();

		$adapter->getProfiler()->profilerStart($sql);
		$statement = $adapter->query($sql);
		$adapter->getProfiler()->profilerFinish();

		$result = $statement->execute();

		if (! $result && $die === true)
		{
			//online if the installer is running ignore this, else THROW error
			if (defined('IS_INSTALLER') && IS_INSTALLER) return [];
			else
			{
				$title = 'Error in the database';
				if ($session['user']['superuser'] & SU_DEVELOPER || 1) $message = '<pre>'.HTMLEntities($sql, ENT_COMPAT, getsetting('charset', 'UTF_8')).'</pre>'.error(LINK);
				else $message = "A most bogus error has occurred.  I apologise, but the page you were trying to access is broken.  Please use your browser's back button and try again. Additionally, report this via petition to somebody from staff with the precise location and what you did. <br/><br/>Thanks";

				die(self::template($title, $message, true));
			}
		}
		$profiler = $adapter->getProfiler()->getProfiles();

		if ($profiler[0]['elapse'] >= 0.5)
			debug(sprintf('Slow Query (%ss): %s',
				round($profiler[0]['elapse'],3),
				HTMLEntities($statement->getSql(), ENT_COMPAT, getsetting('charset', 'UTF_8'))
			));

		$dbinfo['queriesthishit']++;
		$dbinfo['querytime'] += $profiler[0]['elapse'];

		//-- Guardar datos útiles
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
			if (list($key,$val)=each($result)) return $val;
			else return false;
		}
		else return $result->next();
	}

	public static function num_rows($result)
	{
		if (is_array($result)) return count($result);
		else return $result->count();
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
			$objectString = self::sql()->buildSqlString($sql);
			self::$sqlString = $objectString;

			$result = self::query($objectString);
			$data = [];
			while ($row = self::fetch_assoc($result))
			{
				$data[] = $row;
			}
			updatedatacache($name,$data);
			reset($data);

			return $data;
		}
		else
		{
			$result = self::query($sql);
			$data = array();
			while ($row = self::fetch_assoc($result))
			{
				$data[] = $row;
			}
			updatedatacache($name,$data);
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

	//-- Comprobar si una tabla existe
	public static function table_exists($tablename)
	{
		if (defined("DB_NODB") && !defined("LINK")) return false;

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

	//-- Quote value for safe using in DB
	public static function quoteValue($value)
	{
		return self::getAdapter()->getPlatform()->quoteValue($value);
	}

	//-- Función para crear una plantilla y mostrar una página en el die de la conexión
	private static function template($title, $message, $showtrace = false)
	{
		require_once("lib/nltoappon.php");
		require_once("lib/show_backtrace.php");

		$file = file_get_contents('error_docs/template.html');
		$message = full_sanitize(str_replace("`n", "<br />", nltoappon($message)));
		if ($showtrace) $message .= show_backtrace();

		return str_replace(array("{subject}", "{message}"), array($title, $message), $file);
	}

	/**
	 * Funciones propias de Zend
	 */
	private static $sql = null;

	//-- Funciones de base de datos
	public static function sql()
	{
		if (!self::$sql)
		{
			$adapter = self::getAdapter();

			self::$sql = new Sql($adapter);
		}

		return self::$sql;
	}

	public static function select($table = false)
	{
		if ($table) return self::sql()->select($table);
		else  return self::sql()->select();
	}

	public static function insert($table = false)
	{
		if ($table) return self::sql()->insert($table);
		else  return self::sql()->insert();
	}
	public static function update($table = false)
	{
		if ($table) return self::sql()->update($table);
		else  return self::sql()->update();
	}

	public static function delete($table = false)
	{
		if ($table) return self::sql()->delete($table);
		else  return self::sql()->delete();
	}

	public static function execute($object)
	{
		if ('object' != gettype($object)) return false;

		$objectString = self::sql()->buildSqlString($object);
        self::$sqlString = $objectString;

		return self::query($objectString);
	}

    public static function sqlString()
    {
        return self::$sqlString;
    }

	//-- Funciones para paginación
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

        self::$sqlString = $select->getSqlString();

        return $paginator;
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
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 please use "%s" instead',
        __METHOD__,
		'DB::prefix'
    ), E_USER_DEPRECATED);

	return DB::prefix($tablename, $force = false);
}
function db_query($sql, $die = true)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 please use "%s" instead',
        __METHOD__,
		'DB::query'
    ), E_USER_DEPRECATED);

	return DB::query($sql, $die = true);
}
function db_fetch_assoc(&$result)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 no need use this function. Please read documentation of Zend\\Db for see how use this component. Link %s',
        __METHOD__,
		'https://docs.zendframework.com/zend-db/'
    ), E_USER_DEPRECATED);

	return DB::fetch_assoc($result);
}
function db_num_rows($result)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 please use "%s" instead. Please read documentation of Zend\\Db for see how use this component. Link %s',
        __METHOD__,
		'$result->count()',
		'https://docs.zendframework.com/zend-db/'
    ), E_USER_DEPRECATED);

	return DB::num_rows($result);
}
function db_affected_rows($link = false)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 please use "%s" instead. Please read documentation of Zend\\Db for see how use this component. Link %s',
        __METHOD__,
		'$result->getAffectedRows()',
		'https://docs.zendframework.com/zend-db/'
    ), E_USER_DEPRECATED);

	return DB::affected_rows($link);
}
function db_free_result($result)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 please use "%s" instead',
        __METHOD__,
		'DB::free_result'
    ), E_USER_DEPRECATED);

	return DB::free_result($result);
}
function &db_query_cached($sql, $name, $duration = 900)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 please use "%s" instead',
        __METHOD__,
		'DB::query_cached'
    ), E_USER_DEPRECATED);

	return DB::query_cached($sql, $name, $duration = 900);
}
function db_insert_id()
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 please use "%s" instead',
        __METHOD__,
		'DB::insert_id'
    ), E_USER_DEPRECATED);

	return DB::insert_id();
}
function db_error($link = false)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 please use "%s" instead',
        __METHOD__,
		'DB::error'
    ), E_USER_DEPRECATED);

	return DB::error($link);
}
function db_table_exists($tablename)
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 please use "%s" instead',
        __METHOD__,
		'DB::table_exists'
    ), E_USER_DEPRECATED);

	return DB::table_exists($tablename);
}
function db_get_server_version()
{
	trigger_error(sprintf(
        'Usage of %s is obsolete since v0.8.0; and delete in version 2.0.0 please use "%s" instead',
        __METHOD__,
		'DB::get_server_version'
    ), E_USER_DEPRECATED);

	return DB::get_server_version();
}