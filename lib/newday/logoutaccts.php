<?php

$repository = \Doctrine::getRepository('LotgdCore:User');

//-- Logout accounts inactive
$repository->logoutInactiveAccounts((int) LotgdSetting::getSetting('LOGINTIMEOUT', 900));

\LotgdKernel::get('cache.app')->delete('char-list-home-page');
