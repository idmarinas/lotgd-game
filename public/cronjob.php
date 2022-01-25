<?php

use Cron\Cron;
use Cron\Executor\Executor;
use Cron\Job\ShellJob;
use Cron\Resolver\ArrayResolver;
use Cron\Schedule\CrontabSchedule;
use Lotgd\Core\Entity\Cronjob;

//-- Only can invoke in cli (shell)
if ('cli' != substr(PHP_SAPI, 0, 3) || ! empty($_SERVER['REMOTE_ADDR']))
{
    echo 'Only in cli';

    exit;
}

\define('ALLOW_ANONYMOUS', true);

require_once __DIR__.'/common.php';

$cron     = new Cron();
$resolver = new ArrayResolver();

//-- To avoid potential problems with other cache data and optimization/removal processes
$cache = LotgdKernel::get('core.cronjobs.cache');

$cronjobs = $cache->get('tablecronjobs', function ($item)
{
    $item->expiresAt(new DateTime('tomorrow')); //-- Update each day

    $repository = Doctrine::getRepository(Cronjob::class);
    $entities = $repository->findBy(['enabled' => 1]);

    return $repository->extractEntity($entities);
});

//-- Add all cronjobs to Jobby CronJob
foreach ($cronjobs as $job)
{
    $shell = new ShellJob();
    $shell->setCommand("php {$job['command']}.php");
    $shell->setSchedule(new CrontabSchedule($job['schedule']));

    $resolver->addJob($shell);
}

$cron->setExecutor(new Executor());
$cron->setResolver($resolver);
$cron->run();
