<?php

require_once 'lib/configuration/save.php';

$details = gametimedetails();

$setup = include 'lib/data/configuration.php';

$secstonewday = secondstonextgameday($details);
$useful_vals = [
    'dayduration' => round(($details['dayduration'] / 60 / 60), 0).' hours',
    'curgametime' => getgametime(),
    'curservertime' => date('Y-m-d h:i:s a'),
    'lastnewday' => date('h:i:s a', strtotime("-{$details['realsecssofartoday']} seconds")),
    'nextnewday' => date('h:i:s a', strtotime("+{$details['realsecstotomorrow']} seconds")).' ('.date('H\\h i\\m s\\s', $secstonewday).')'
];

$vals = $settings->getArray() + $useful_vals;

$params['form'] = lotgd_showform($setup, $vals, false, false, false);
