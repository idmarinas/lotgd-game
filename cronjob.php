<?php

//-- Only can invoke in cli (shell)
if(substr(php_sapi_name(), 0, 3) != 'cli' || ! empty($_SERVER['REMOTE_ADDR']))
{
    echo 'Only in cli';

    exit;
}

define('ALLOW_ANONYMOUS', true);

require_once __DIR__ . '/common.php';

$jobby = new Jobby\Jobby();

$lotgd_cache->getOptions()->setNamespace('cronjob');//-- For avoid possible problems with other cache
$cronjobs = datacache('tablecronjobs', 86400, true);//-- Cache for 1 day
if (! is_array($cronjobs) || empty($cronjobs))
{
    $select = DB::select('cronjob');
    $select->columns(['*'])
        ->where->equalTo('enabled', 1)
    ;
    $cronjobs = DB::toArray(DB::execute($select));

    updatedatacache('tablecronjobs', $cronjobs, true);
}

//-- Add all cronjobs to Jobby CronJob
foreach ($cronjobs as $key => $job)
{
    $job = array_filter($job);

    $jobName = $job['name'];
    $job['command'] = 'php ' . $job['command'] . '.php';
    unset($job['name']);
    $jobby->add($jobName, $job);
}

$jobby->run();
