<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 3.0.0
 */

namespace Lotgd\Core\Db;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Profiler\Profiler;
use Laminas\Db\ResultSet\ResultSet;

/**
 * Class for access to data base.
 *
 * @deprecated 4.4.0 deleted in version 5.0.0
 */
class Dbwrapper
{
    use \Lotgd\Core\Pattern\Container;
    use Pattern\DbTool;
    use Pattern\Prefix;
    use Pattern\Zend;

    protected $adapter;
    protected $generatedValue;
    protected $affectedRows = 0;
    protected $errorInfo;
    protected $sqlString;

    protected $queriesthishit = 0;
    protected $querytime      = 0;

    /**
     * Configure adapter for DB.
     *
     * @param bool $force
     *
     * @return $this
     */
    public function __construct(array $options, $force = null)
    {
        if ( ! isset($options['driver']) || '' == $options['driver'])
        {
            $options['driver'] = 'Pdo_Mysql';
        }

        if ('pdo_mysql' == \strtolower($options['driver']))
        {
            $options['driver_options'] = [
                \PDO::MYSQL_ATTR_FOUND_ROWS => true,
            ];
        }

        if ( ! $this->adapter || true === $force)
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
     * @return ResultSet
     */
    public function query(string $sql)
    {
        //-- Not do query if not exist connection to DB
        if (false === $this->connect())
        {
            $resultSet = new ResultSet();

            return $resultSet->initialize([]);
        }

        $adapterNew = $this->getAdapter();

        try
        {
            $adapterNew->getProfiler()->profilerStart($sql);
            $statement = $adapterNew->query($sql);
            $adapterNew->getProfiler()->profilerFinish();

            $result = $statement->execute();
        }
        catch (\Throwable $ex)
        {
            $this->errorInfo = $ex->getMessage();
            $resultSet       = new ResultSet();

            return $resultSet->initialize([]);
        }

        $profiler = $adapterNew->getProfiler()->getLastProfile();

        if ($profiler['elapse'] >= 0.5)
        {
            \LotgdResponse::pageDebug(\sprintf(
                'Slow Query (%ss): %s',
                \round($profiler['elapse'], 3),
                \htmlentities($statement->getSql(), ENT_COMPAT, 'UTF-8')
            ));
        }

        ++$this->queriesthishit;
        $this->querytime += $profiler['elapse'];

        //-- Save data for usage
        $this->generatedValue = $result->getGeneratedValue();
        $this->affectedRows   = $result->getAffectedRows();
        $this->errorInfo      = $result->getResource()->errorInfo()[2];
        $this->sqlString      = $statement->getSql();

        return $result;
    }

    public function getAffectedRows($result = null): int
    {
        if (\is_object($result) && \method_exists($result, 'getAffectedRows'))
        {
            return $result->getAffectedRows();
        }

        return $this->affectedRows;
    }

    public static function current(&$result)
    {
        if (\is_array($result))
        {
            $val = \current($result);
            \next($result);

            return $val;
        }
        elseif (\is_object($result))
        {
            return $result->next();
        }

        return $result;
    }

    public static function count($result): int
    {
        if (\is_array($result))
        {
            return \count($result);
        }
        elseif (\is_object($result))
        {
            return $result->count();
        }

        return (int) $result;
    }

    public function getQueriesThisHit(): int
    {
        return $this->queriesthishit;
    }

    public function getQueryTime(): float
    {
        return (float) $this->querytime;
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
     * @param object $result
     */
    public function errorInfo($result = null)
    {
        if (null !== $result && \is_object($result))
        {
            return $result->getResource()->errorInfo()[2];
        }

        return $this->errorInfo;
    }

    /**
     * Get adapter for DB.
     */
    public function getAdapter(): Adapter
    {
        if ( ! $this->adapter)
        {
            $request = $this->getContainer(\Lotgd\Core\Http\Request::class);
            \LotgdResponse::pageStart('Database Connection Error');
            \LotgdResponse::pageAddContent(\LotgdFormat::colorize('`c`$Database Connection Error`0´c`n`n'));
            \LotgdResponse::pageAddContent(\LotgdFormat::colorize('`xDue to technical problems the game is unable to connect to the database server.`n`n'));

            //the admin did not want to notify him with a script
            \LotgdResponse::pageAddContent(\LotgdFormat::colorize('Please notify the head admin or any other staff member you know via email or any other means you have at hand to care about this.`n`n'));
            \LotgdResponse::pageAddContent(\LotgdFormat::colorize('Sorry for the inconvenience,`n'));
            \LotgdResponse::pageAddContent(\LotgdFormat::colorize(\sprintf('Staff of %s', $request->getServer('SERVER_NAME')), true));
            \LotgdNavigation::addNav('Home', 'index.php');
            \LotgdResponse::pageEnd();
        }

        return $this->adapter;
    }
}
