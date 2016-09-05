<?php
// addnews ready
// translator ready
// mail ready
require_once("lib/errorhandling.php");
require_once("lib/datacache.php");
require_once("settings.php");
/* * * *
 * Avaiable values for DBTYPE:
 *
 *  mysqli:     The ext/mysqli driver
 *  pgsql:      The ext/pgsql driver
 *  sqlsrv:     The ext/sqlsrv driver (from Microsoft)
 *  mysql:      MySQL through the PDO extension -> DEFAULT
 *  sqlite:     SQLite though the PDO extension
 *  pgsql:      PostgreSQL through the PDO extension
 *
 */
define('DBTYPE',$DB_TYPE);

$dbinfo = array();
$dbinfo['queriesthishit']=0;
$dbinfo['querytime']=0;

require_once './lib/dbwrapper_pdo.php';

if ('mysqli' == strtolower(DBTYPE)) $driver = 'Mysqli';
else if ('pgsql' == strtolower(DBTYPE)) $driver = 'Pgsql';
else if ('sqlsrv' == strtolower(DBTYPE)) $driver = 'Sqlsrv';
else if ('sqlite' == strtolower(DBTYPE)) $driver = 'Pdo_Sqlite';
else if ('pgsql' == strtolower(DBTYPE)) $driver = 'Pdo_Pgsql';
else $driver = 'PDO_Mysql';

DB::setSettings(['driver' => $driver]);
unset($driver, $DB_TYPE);