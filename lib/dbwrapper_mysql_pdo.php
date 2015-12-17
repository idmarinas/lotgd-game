<?php
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Profiler\Profiler;
use Zend\Db\Metadata\Metadata;

Class DB 
{
	private static $adapter;
	private static $generatedValue = null;
	private static $affectedRows = 0;
	private static $errorInfo = null;
	
	//-- Configura el adaptador
	public static function setAdapter(Array $options)
	{
		$adapter = new Adapter($options);
		$adapter->setProfiler(new Profiler());
		
		if (!self::$adapter) self::$adapter = $adapter;
	}
	
	//-- Obtiene el adaptador
	private static function getAdapter()
	{
		if (!self::$adapter)
		{
			$title = 'Error en la base de datos';
			$message = 'Se ha producido un error al conectar a la base de datos del juego. `nPor favor espere uno minutos, si el problema continua pongase en contacto con los administradores.';
			
			die(self::template($title, $message));
		}
		return self::$adapter;
	}
	
	//-- Realizar la conexión a la base de datos
	public static function connect()
	{
		$adapter = self::getAdapter();
		$driver = $adapter->getDriver();
		$connection = $driver->getConnection();
		
		// return $connection->isConnected();
		return true;
	}
	
	//-- Seleccionar la base de datos
	public static function select_db($dbname)
	{
		$fname = DBTYPE."_select_db";
		$r = $fname($dbname);
		
		return $r;
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
	
	//-- Realizar una consulta a la base de datos
	public static function query($sql, $die = true)
	{
		if (defined("DB_NODB") && !defined("LINK")) return array();
		
		global $session, $dbinfo;
		
		$adapter = self::getAdapter();
				
		$adapter->getProfiler()->profilerStart($sql);
		$statement = $adapter->query($sql);
		$adapter->getProfiler()->profilerFinish();
		
		$result = $statement->execute();
		
		if (!$result && $die === true) 
		{
			//online if the installer is running ignore this, else THROW error
			if (defined("IS_INSTALLER") && IS_INSTALLER) return array();
			else
			{
				if ($session['user']['superuser'] & SU_DEVELOPER || 1)
				{
					$title = 'Error en la base de datos';
					$message = "<pre>".HTMLEntities($sql, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</pre>"
						.error(LINK);
			
					die(self::template($title, $message, true));
				}
				else 
				{
					$title = 'Error en la base de datos';
					$message = "A most bogus error has occurred.  I apologise, but the page you were trying to access is broken.  Please use your browser's back button and try again. Additionally, report this via petition to somebody from staff with the precise location and what you did. <br/><br/>Thanks";
					
					die(self::template($title, $message, true));
				}
			}
		}
		$profiler = $adapter->getProfiler()->getProfiles();
		$info = $profiler[0];

		if ($info['elapse'] >= 0.5)
			debug("Slow Query (".round($info['elapse'],3)."s): ".(HTMLEntities($statement->getSql(), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))));
			
		$dbinfo['queriesthishit']++;
		$dbinfo['querytime'] += $info['elapse'];
				
		//-- Guardar tados útiles
		self::$generatedValue = $result->getGeneratedValue();
		self::$affectedRows = $result->getAffectedRows();
		self::$errorInfo = 	$result->getResource()->errorInfo();
						
		return $result;
		
	// 'sql' => 'SELECT * FROM settings',
    // 'parameters' => NULL,
    // 'start' => 1450281530.4497969150543212890625,
    // 'end' => 1450281530.4507191181182861328125,
    // 'elapse' => 0.00092220306396484375,
	}
	
	//-- Asociar los valores del resultado
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
	
	//-- Contar el número de resultados
	public static function num_rows($result)
	{
		if (is_array($result)) return count($result);
		else return $result->count();
	}
	
	//-- Número de filas afectadas por la consulta
	public static function affected_rows($result = false)
	{
		if (false === $result) return self::$affectedRows;
		else return $result->getAffectedRows();
	}
	
	//-- Obtener el ID de ultimo registro insertado
	public static function insert_id()
	{
		return self::$generatedValue;
	}
	
	//-- Liberar resultados
	public static function free_result($result)
	{
		if (is_array($result)) unset($result);//cached data			
		else return true;
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
		$fname = DBTYPE."_error";
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
		$adapter = self::getAdapter();
		
		return $adapter->getPlatform()->getName();
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
}

/**
 * Funciones legado, para que funcionen en todos los módulso y partes del juego
 */
function db_connect($host, $user, $pass, $database)
{
	$adapter = [
		'driver' => 'PDO_Mysql',
		'hostname' => $host,
		'database' => $database,
		'charset' => 'utf8',
		'username' => $user,
		'password' => $pass
	];
	
	DB::setAdapter($adapter);
	
	return DB::connect();
}
function db_pconnect($host, $user, $pass, $database)
{
	return DB::connect($host, $user, $pass, $database);
}
function db_select_db($dbname)
{
	return true;
}
function db_prefix($tablename, $force = false)
{
	return DB::prefix($tablename, $force = false);
}
function db_query($sql, $die = true)
{
	return DB::query($sql, $die = true);
}
function db_fetch_assoc(&$result)
{
	return DB::fetch_assoc($result);
}
function db_num_rows($result)
{
	return DB::num_rows($result);
}
function db_affected_rows($link = false)
{
	return DB::affected_rows($link);
}
function db_free_result($result)
{
	return DB::free_result($result);
}
function &db_query_cached($sql, $name, $duration = 900)
{
	return DB::query_cached($sql, $name, $duration = 900);
}
function db_insert_id()
{
	return DB::insert_id();
}
function db_error($link = false)
{
	return DB::error($link);
}
function db_table_exists($tablename)
{
	return DB::table_exists($tablename);
}
function db_get_server_version()
{
	return DB::get_server_version();
}