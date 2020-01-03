<?php

require_once 'lib/configuration/save.php';

$options = LotgdLocator::get('GameConfig');

$params['gameCaches'] = ($options['caches']);

