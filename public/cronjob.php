<?php

//-- Only can invoke in cli (shell)
if ('cli' != substr(php_sapi_name(), 0, 3) || ! empty($_SERVER['REMOTE_ADDR']))
{
    echo 'Only in cli';

    exit;
}

define('ALLOW_ANONYMOUS', true);

require_once __DIR__.'/common.php';

$cron = new \Cron\Cron();
$resolver = new \Cron\Resolver\ArrayResolver();

//-- To avoid potential problems with other cache data and optimization/removal processes
$cronCache = \LotgdLocator::get('Cache\Core\Cronjob');

$cronjobs = $cronCache->getItem('tablecronjobs'); //-- Cache for 1 day
if (! is_array($cronjobs) || empty($cronjobs))
{
    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Cronjob::class);
    $entities = $repository->findBy(['enabled' => 1]);
    $cronjobs = $repository->extractEntity($entities);

    $cronCache->setItem('tablecronjobs', $cronjobs);
}

//-- Add all cronjobs to Jobby CronJob
foreach ($cronjobs as $key => $job)
{
    $shell = new \Cron\Job\ShellJob();
    $shell->setCommand("php {$job['command']}.php");
    $shell->setSchedule(new \Cron\Schedule\CrontabSchedule($job['schedule']));

    $resolver->addJob($shell);
}

$cron->setExecutor(new \Cron\Executor\Executor());
$cron->setResolver($resolver);
$cron->run();
