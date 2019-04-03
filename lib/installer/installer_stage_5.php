<?php

require_once 'lib/installer/installer_functions.php';

use Doctrine\Common\EventManager as DoctrineEventManager;
use Doctrine\ORM\Configuration as DoctrineConfiguration;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\Events as DoctrineEvents;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy as DoctrineUnderscoreNamingStrategy;
use Lotgd\Core\Doctrine\Extension\TablePrefix as DoctrineTablePrefix;
use Lotgd\Core\Doctrine\Strategy\Quote as DoctrineQuoteStrategy;

$session['installer']['dbinfo']['DB_PREFIX'] = \LotgdHttp::getPost('DB_PREFIX') ?: '';

if ($session['installer']['dbinfo']['DB_PREFIX'] > '' && '_' != substr($session['installer']['dbinfo']['DB_PREFIX'], -1))
{
    $session['installer']['dbinfo']['DB_PREFIX'] .= '_';
}

//Note: this is mysql only, we should maybe rewrite that part. :/
//Or we could save ourselves the dbtype stuff

//-- Settings for Database Adapter
$adapter = new Lotgd\Core\Db\Dbwrapper([
    'driver' => $session['installer']['dbinfo']['DB_DRIVER'],
    'hostname' => $session['installer']['dbinfo']['DB_HOST'],
    'database' => $session['installer']['dbinfo']['DB_NAME'],
    'charset' => 'utf8',
    'username' => $session['installer']['dbinfo']['DB_USER'],
    'password' => $session['installer']['dbinfo']['DB_PASS']
]);
$adapter->setPrefix($session['installer']['dbinfo']['DB_PREFIX']);
//-- Configure DB
DB::wrapper($adapter);

$link = DB::connect();

/**
 * Configure Doctrine.
 */
$config = new DoctrineConfiguration();
$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(['src/core/Entity'], false));
$config->setProxyDir('cache/doctrine/Proxy');
$config->setProxyNamespace('Lotgd\Installer\Proxies');

$config->setNamingStrategy(new DoctrineUnderscoreNamingStrategy(CASE_LOWER));
$config->setQuoteStrategy(new DoctrineQuoteStrategy());

$evm = new DoctrineEventManager();
$tablePrefix = new DoctrineTablePrefix($session['installer']['dbinfo']['DB_PREFIX']);
$evm->addEventListener(DoctrineEvents::loadClassMetadata, $tablePrefix);

$doctrineManager = DoctrineEntityManager::create([
    'driver' => strtolower($session['installer']['dbinfo']['DB_DRIVER']),
    'host' => $session['installer']['dbinfo']['DB_HOST'],
    'user' => $session['installer']['dbinfo']['DB_USER'],
    'password' => $session['installer']['dbinfo']['DB_PASS'],
    'dbname' => $session['installer']['dbinfo']['DB_NAME'],
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

$upgrade = ($missing * 10 < $game);//-- Looks like an upgrade

if ('install' == \LotgdHttp::getQuery('type'))
{
    $upgrade = false;
}
elseif ('upgrade' == \LotgdHttp::getQuery('type'))
{
    $upgrade = true;
}

$session['installer']['dbinfo']['upgrade'] = $upgrade;

if ($upgrade && count($conflict) > 0)
{
    if ('confirm_overwrite' == \LotgdHttp::getQuery('op'))
    {
        $session['sure i want to overwrite the tables'] = true;
    }

    if (! ($session['sure i want to overwrite the tables'] ?? false))
    {
        $session['installer']['stagecompleted'] = 4;
        $params['increasedStage'] = true;
    }
}
$params = [
    'update' => $upgrade,
    'conflict' => $conflict,
    'DB_PREFIX' => $session['installer']['dbinfo']['DB_PREFIX'] ?? ''
];

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/installer/stage-5.twig', $params));
