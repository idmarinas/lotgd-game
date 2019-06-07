<?php

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);

//-- Logout accounts inactive
$repository->logoutInactiveAccounts((int) getsetting('LOGINTIMEOUT', 900));
