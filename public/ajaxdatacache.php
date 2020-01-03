<?php

global $session, $fiveminuteload;

define('ALLOW_ANONYMOUS', false);
define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

$op = (string) \LotgdHttp::getQuery('op');
$class = (string) \LotgdHttp::getQuery('cache');

$textDomain = 'ajax-datacache';

if (\LotgdLocator::has($class))
{
    $cache = \LotgdLocator::get($class);

    if ('optimize' == $op)
    {
        $cache->optimize();

        echo 'ok';
    }
    elseif ('clearexpire' == $op)
    {
        $cache->clearExpired();

        echo 'ok';
    }
    elseif ('clearall' == $op)
    {
        $result = 'ok';

        try
        {
            $cache->flush();
        }
        catch (\Exception $ex)
        {
            //-- With this avoid a 500 server error
            //-- In some cases it may not be possible to delete certain files and directories because it not have permissions.
            $result = \LotgdTranslator::t('clear.all', [], $textDomain);
        }

        echo $result;
    }
    elseif ('clearbyprefix' == $op)
    {
        $prefix = \LotgdHttp::getQuery('prefix');

        $cache->clearByPrefix($prefix);

        echo 'ok';
    }
    else
    {
        echo \LotgdTranslator::t('not.found', [], $textDomain);
    }
}
else
{
    echo \LotgdTranslator::t('factory', ['name' => $class], $textDomain);
}
