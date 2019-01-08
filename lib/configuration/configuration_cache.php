<?php

$setup_cache = include_once 'lib/data/configuration_cache.php';

$wrapper = LotgdLocator::get(\Lotgd\Core\Db\Dbwrapper::class);
$adapter = $wrapper->getAdapter();
$platform = $adapter->getPlatform();
$options = LotgdLocator::get('GameConfig');

$vals = [
    'datacachepath' => $options['cache']['config']['cache_dir'] ?? 'cache',
    'usedatacache' => (int) ($options['cache']['active'] ?? 0),
    'databasetype' => $platform->getName(),
];

output_notl(LotgdTheme::renderLotgdTemplate('configuration/cache.twig', []), true);
output_notl('`n`n');
output('High Load Optimization:`n`n');
rawoutput("<form action='configuration.php?settings=cache&op=save' method='POST'>");
lotgd_showform($setup_cache, $vals, true);
rawoutput('</form>');
