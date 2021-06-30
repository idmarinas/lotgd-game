<?php

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);

//-- Logout accounts inactive
$repository->logoutInactiveAccounts((int) LotgdSetting::getSetting('LOGINTIMEOUT', 900));

\LotgdKernel::get('cache.app')->delete('char-list-home-page');
