<?php

require_once 'lib/installer/installer_functions.php';

use Doctrine\Common\EventManager as DoctrineEventManager;
use Doctrine\ORM\Configuration as DoctrineConfiguration;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\Events as DoctrineEvents;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy as DoctrineUnderscoreNamingStrategy;
use Lotgd\Core\Doctrine\Extension\TablePrefix as DoctrineTablePrefix;
use Lotgd\Core\Doctrine\Strategy\Quote as DoctrineQuoteStrategy;

$session['dbinfo']['DB_PREFIX'] = httppost('DB_PREFIX') ?: '';

if ($session['dbinfo']['DB_PREFIX'] > '' && '_' != substr($session['dbinfo']['DB_PREFIX'], -1))
{
    $session['dbinfo']['DB_PREFIX'] .= '_';
}

//Note: this is mysql only, we should maybe rewrite that part. :/
//Or we could save ourselves the dbtype stuff

//-- Settings for Database Adapter
$adapter = new Lotgd\Core\Db\Dbwrapper([
    'driver' => $session['dbinfo']['DB_DRIVER'],
    'hostname' => $session['dbinfo']['DB_HOST'],
    'database' => $session['dbinfo']['DB_NAME'],
    'charset' => 'utf8',
    'username' => $session['dbinfo']['DB_USER'],
    'password' => $session['dbinfo']['DB_PASS']
]);
$adapter->setPrefix($session['dbinfo']['DB_PREFIX']);
//-- Configure DB
DB::wrapper($adapter);

$link = DB::connect();

/**
 * Configure Doctrine.
 */
$config = new DoctrineConfiguration();
$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(['src/core/Entity'], false));
$config->setProxyDir($session['dbinfo']['DB_DATACACHEPATH'] ?? 'cache/' . 'doctrine/Proxy');
$config->setProxyNamespace('Lotgd\Installer\Proxies');

$config->setNamingStrategy(new DoctrineUnderscoreNamingStrategy(CASE_LOWER));
$config->setQuoteStrategy(new DoctrineQuoteStrategy());

$evm = new DoctrineEventManager();
$tablePrefix = new DoctrineTablePrefix($session['dbinfo']['DB_PREFIX']);
$evm->addEventListener(DoctrineEvents::loadClassMetadata, $tablePrefix);

$doctrineManager = DoctrineEntityManager::create([
    'driver' => strtolower($session['dbinfo']['DB_DRIVER']),
    'host' => $session['dbinfo']['DB_HOST'],
    'user' => $session['dbinfo']['DB_USER'],
    'password' => $session['dbinfo']['DB_PASS'],
    'dbname' => $session['dbinfo']['DB_NAME'],
    'charset' => 'utf8'
], $config, $evm);

$schemaManager = $doctrineManager->getConnection()->getSchemaManager();
$metadata = $doctrineManager->getMetadataFactory()->getAllMetadata();

//-- List tables of data base
$listTableNames = $schemaManager->listTableNames();
//-- List tables of Core Game
$tableNames = [];
foreach($metadata as $key => $value)
{
    $tableNames[$key] = $value->getTableName();
}

$conflict = array_intersect($tableNames, $listTableNames);
$game = count($conflict);
$missing = count($tableNames) - $game;

$upgrade = ($missing * 10 < $game);//looks like an upgrade

if ('install' == httpget('type'))
{
    $upgrade = false;
}
elseif ('upgrade' == httpget('type'))
{
    $upgrade = true;
}

$session['dbinfo']['upgrade'] = $upgrade;

if ($upgrade)
{
    output('`c`@This looks like a game upgrade.`0´c`n');
    output("`^If this is not an upgrade from a previous version of LoGD, <a href='installer.php?stage=5&type=install'>click here</a>.", true);
    output('`2Otherwise, continue on to the next step.');
}
else
{
    //looks like a clean install
    $upgrade = false;
    output('`c`@This looks like a fresh install.`0´c`n');
    output("`2If this is not a fresh install, but rather an upgrade from a previous version of LoGD, chances are that you installed LoGD with a table prefix.  If that's the case, enter the prefix below.  If you are still getting this message, it's possible that I'm just spooked by how few tables are common to the current version, and in which case, I can try an upgrade if you <a href='installer.php?stage=5&type=upgrade'>click here</a>.`n", true);

    if (count($conflict) > 0)
    {
        output('`n`n`$There are table conflicts.`2');
        output("If you continue with an install, the following tables will be overwritten with the game's tables.  If the listed tables belong to LoGD, they will be upgraded, otherwise all existing data in those tables will be destroyed.  Once this is done, this cannot be undone unless you have a backup!`n");
        output('`nThese tables conflict: `^'.join(', ', $conflict).'`2`n');

        if ('confirm_overwrite' == httpget('op'))
        {
            $session['sure i want to overwrite the tables'] = true;
        }

        if (! isset($session['sure i want to overwrite the tables']) || ! $session['sure i want to overwrite the tables'])
        {
            $session['installer']['stagecompleted'] = 4;
            output("`nIf you are sure that you wish to overwrite these tables, <a href='installer.php?stage=5&op=confirm_overwrite'>click here</a>.`n", true);
        }
    }
    output('`nYou can avoid table conflicts with other applications in the same database by providing a table name prefix.');
    output('This prefix will get put on the name of every table in the database.');
}

//Display rights - I won't parse them, sue me for laziness, and this should work nicely to explain any errors
$result = DB::query('SHOW GRANTS FOR CURRENT_USER()');
output("`2These are the rights for your mysql user, `\$make sure you have the 'LOCK TABLES' privileges OR a \"GRANT ALL PRIVLEGES\" on the tables.`2`n`n");
output('If you do not know what this means, ask your hosting provider that supplied you with the database credentials.`n`n');

rawoutput("<table class='ui very compact striped selectable table'>");
$i = 0;
foreach ($result as $row)
{
    if (0 == $i)
    {
        rawoutput("<tr>");
        $keys = array_keys($row);

        foreach ($keys as $value)
        {
            rawoutput("<th>$value</th>");
        }
        rawoutput('</tr>');
    }
    rawoutput("<tr>");

    foreach ($keys as $value)
    {
        rawoutput("<td>{$row[$value]}</td>");
    }

    rawoutput('</tr>');
    $i++;
}
rawoutput('</table>');
//done

output('`nTo provide a table prefix, enter it here.');
output("If you don't know what this means, you should either leave it blank, or enter an intuitive value such as \"logd\".`n");
rawoutput("<form action='installer.php?stage=5' method='POST' class='ui form'><div class='ui action input'>");
rawoutput("<input name='DB_PREFIX' value=\"".htmlentities($session['dbinfo']['DB_PREFIX'], ENT_COMPAT, getsetting('charset', 'UTF-8')).'"><br>');
$submit = translate_inline('Submit your prefix.');
rawoutput("<button type='submit' class='ui button'>$submit</button>");
rawoutput('</div></form>');

if (0 == count($conflict))
{
    output("`^It looks like you can probably safely skip this step if you don't know what it means.");
}
output('`n`n`@Once you have submitted your prefix, you will be returned to this page to select the next step.');
output("If you don't need a prefix, just select the next step now.");
