<?php

//-- Only can invoke in cli (shell)
if ('cli' != substr(php_sapi_name(), 0, 3) || ! empty($_SERVER['REMOTE_ADDR']))
{
    echo 'Only in cli';

    exit;
}

define('ALLOW_ANONYMOUS', true);

require_once __DIR__.'/common.php';

$jobby = new Jobby\Jobby();

//-- To avoid potential problems with other cache data and optimization/removal processes
$cronCache = \LotgdLocator::get('Cache\Core\Cronjob');

$cronjobs = $cronCache->getData('tablecronjobs', 86400, true); //-- Cache for 1 day
if (! is_array($cronjobs) || empty($cronjobs))
{
    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Cronjob::class);
    $entities = $repository->findBy(['enabled' => 1]);
    $cronjobs = $repository->extractEntity($entities);

    $cronCache->updateData('tablecronjobs', $cronjobs, true);
}

//-- Add all cronjobs to Jobby CronJob
foreach ($cronjobs as $key => $job)
{
    $job = array_filter($job);

    $jobName = $job['name'];
    $job['command'] = "php {$job['command']}.php";
    unset($job['name']);
    $jobby->add($jobName, $job);
}

$jobby->run();
