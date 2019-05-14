<?php

require_once 'lib/configuration/save.php';

$setup_cache = include_once 'lib/data/configuration_cache.php';

$options = LotgdLocator::get('GameConfig');

$params['coreCacheFolder'] = $options['lotgd_core']['cache']['config']['cache_dir'];
$params['cacheActive'] = (bool) $options['lotgd_core']['cache']['active'];
