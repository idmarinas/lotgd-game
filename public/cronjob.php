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
$cache = \LotgdKernel::get('core.cronjobs.cache');

$cronjobs = $cache->get('tablecronjobs', function ($item)
{
    $item->expiresAt(new \DateTime('tomorrow')); //-- Update each day

    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Cronjob::class);
    $entities = $repository->findBy(['enabled' => 1]);

    return $repository->extractEntity($entities);
});

//-- Add all cronjobs to Jobby CronJob
foreach ($cronjobs as $job)
{
    $shell = new \Cron\Job\ShellJob();
    $shell->setCommand("php {$job['command']}.php");
    $shell->setSchedule(new \Cron\Schedule\CrontabSchedule($job['schedule']));

    $resolver->addJob($shell);
}

$cron->setExecutor(new \Cron\Executor\Executor());
$cron->setResolver($resolver);
$cron->run();
