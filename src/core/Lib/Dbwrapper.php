<?php

// addnews ready
// translator ready
// mail ready

namespace Lotgd\Core\Lib;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Profiler\Profiler;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
// use Zend\Db\Metadata\Metadata;
use Zend\Db\Sql\Update;
use Zend\Paginator\Adapter\DbSelect as DbSelectPaginator;
use Zend\Paginator\Paginator;

class Dbwrapper
{
    protected $adapter;
    protected $prefix = '';
    protected $sql = null;
    protected $generatedValue = null;
    protected $affectedRows = 0;
    protected $errorInfo = null;
    protected $sqlString = null;

    protected $queriesthishit = 0;
    protected $querytime = 0;

    /**
     * Configure adapter for DB.
     *
     * @param array $options
     * @param bool  $force
     *
     * @return $this
     */
    public function __construct(array $options, $force = false)
    {
        if (! isset($options['driver']) || '' == $options['driver'])
        {
            $options['driver'] = 'Pdo_Mysql';
        }

        if ('Pdo_Mysql' == $options['driver'])
        {
            $options['driver_options'] = [
                \PDO::MYSQL_ATTR_FOUND_ROWS => true
            ];
        }

        if (! $this->adapter || true === $force)
        {
            $adapter = new Adapter($options);
            $adapter->setProfiler(new Profiler());

            $this->adapter = $adapter;
        }

        return $this;
    }

    /**
     * Execute a query.
     *
     * @param string $sql
     *
     * @return ResultSet
     */
    public function query($sql)
    {
        // global $dbinfo;

        $adapter = $this->getAdapter();

        try
        {
            $adapter->getProfiler()->profilerStart($sql);
            $statement = $adapter->query($sql);
            $adapter->getProfiler()->profilerFinish();

            $result = $statement->execute();
        }
        catch (\Throwable $ex)
        {
            $this->errorInfo = $ex->getMessage();

            $resultSet = new ResultSet();

            return $resultSet->initialize([]);
        }

        $profiler = $adapter->getProfiler()->getLastProfile();

        if ($profiler['elapse'] >= 0.5)
        {
            debug(sprintf('Slow Query (%ss): %s',
                round($profiler['elapse'], 3),
                htmlentities($statement->getSql(), ENT_COMPAT, 'UTF-8')
            ));
        }

        $this->queriesthishit++;
        $this->querytime += $profiler['elapse'];

        //-- Save data for usage
        $this->generatedValue = $result->getGeneratedValue();
        $this->affectedRows = $result->getAffectedRows();
        $this->errorInfo = $result->getResource()->errorInfo();
        $this->sqlString = $statement->getSql();

        return $result;
    }

    public function getAffectedRows($result = false): int
    {
        if ('object' == gettype($result))
        {
            return $result->getAffectedRows();
        }
        else
        {
            return $this->affectedRows;
        }
    }

    public static function current(&$result)
    {
        if (is_array($result))
        {
            $val = current($result);
            next($result);

            return $val;
        }
        elseif ('object' == gettype($result))
        {
            return $result->next();
        }
        else
        {
            $result;
        }
    }

    public static function count($result): int
    {
        if (is_array($result))
        {
            return count($result);
        }
        elseif ('object' == gettype($result))
        {
            return $result->count();
        }
        else
        {
            return (int) $result;
        }
    }

    /**
     * Select API.
     *
     * @param null|string|array|TableIdentifier $table
     * @param bool                              $prefixed
     *
     * @return object
     */
    public function select($table = null, bool $prefixed = true)
    {
        $table = $prefixed ? $this->prefix($table) : $table;

        if ($table)
        {
            return new Select($table);
        }
        else
        {
            return new Select();
        }
    }

    /**
     * Insert API.
     *
     * @param null|string|TableIdentifier $table
     * @param bool                        $prefixed
     *
     * @return object
     */
    public function insert($table = null, bool $prefixed = true)
    {
        $table = $prefixed ? $this->prefix($table) : $table;

        if ($table)
        {
            return new Insert($table);
        }
        else
        {
            return new Insert();
        }
    }

    /**
     * Update API.
     *
     * @param null|string|TableIdentifier $table
     * @param bool                        $prefixed
     *
     * @return object
     */
    public function update($table = null, bool $prefixed = true)
    {
        $table = $prefixed ? $this->prefix($table) : $table;

        if ($table)
        {
            return new Update($table);
        }
        else
        {
            return new Update();
        }
    }

    /**
     * Delete API.
     *
     * @param null|string|TableIdentifier $table
     * @param bool                        $prefixed
     *
     * @return object
     */
    public function delete($table = null, bool $prefixed = true)
    {
        $table = $prefixed ? $this->prefix($table) : $table;

        if ($table)
        {
            return new Delete($table);
        }
        else
        {
            return new Delete();
        }
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
        $this->queriesthishit++;

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
        return $this->getAdapter()->getPlatform()->quoteValue($value);
    }

    /**
     * Prefix for tables.
     *
     * @param string|array $tablename Name of table
     * @param false|string $force     If you want to force a prefix
     *
     * @return string|array
     */
    public function prefix($tablename, $force = false)
    {
        if (false === $force)
        {
            // The following file should be used to override or modify the
            // special_prefixes array to be correct for your site.  Do NOT
            // do this unles you know EXACTLY what this means to you, your
            // game, your county, your state, your nation, your planet and
            // your universe!
            // Example: you change name of a table
            if (file_exists('prefixes.php'))
            {
                $special_prefixes = include_once 'prefixes.php';
            }

            $prefix = $this->getPrefix();

            if (isset($special_prefixes[$tablename]))
            {
                $prefix = $special_prefixes[$tablename];
            }
        }
        else
        {
            return $prefix = $force;
        }

        if (is_array($tablename))
        {
            list($key, $value) = each($tablename);

            return [$key => $prefix.$value];
        }
        else
        {
            return $prefix.$tablename;
        }
    }

    /**
     * Get the value of prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Set the value of prefix.
     *
     * @param string $prefix
     *
     * @return self
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getQueriesThisHit(): int
    {
        return $this->queriesthishit;
    }

    public function getQueryTime(): float
    {
        return (float) $this->querytime;
    }

    public function sql()
    {
        if (! $this->sql)
        {
            $this->sql = new Sql($this->getAdapter());
        }

        return $this->sql;
    }

    /**
     * Get a sql query string.
     *
     * @return string
     */
    public function getSql()
    {
        return $this->sqlString;
    }

    public function getGeneratedValue()
    {
        return $this->generatedValue;
    }

    /**
     * Get error of connection.
     *
     * @param object|false $result
     */
    public function errorInfo($result = false)
    {
        if (false !== $result)
        {
            $r = $result->getResource()->errorInfo();
        }
        else
        {
            $r = $this->errorInfo;
        }

        return $r;
    }

    /**
     * Get adapter for DB.
     *
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getAdapter(): Adapter
    {
        if (! $this->adapter)
        {
            page_header('Database Connection Error');
            output('`c`$Database Connection Error`0`c`n`n');
            output('`xDue to technical problems the game is unable to connect to the database server.`n`n');

            //the admin did not want to notify him with a script
            output('Please notify the head admin or any other staff member you know via email or any other means you have at hand to care about this.`n`n');
            output('Sorry for the inconvenience,`n');
            output('Staff of %s', $_SERVER['SERVER_NAME']);
            addnav('Home', 'index.php');
            page_footer();
        }

        return $this->adapter;
    }
}
