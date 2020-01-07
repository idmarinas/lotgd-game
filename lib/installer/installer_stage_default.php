<?php

if ($session['user']['loggedin'] ?? false)
{
    \LotgdNavigation::addNav('common.nav.continue', $session['user']['restorepage']);
}
else
{
    \LotgdNavigation::addNav('common.nav.loginScreen', 'home.php');
}
savesetting('installer_version', \Lotgd\Core\Application::VERSION);
$noinstallnavs = true;

//-- Delete info of installation
unset($session['installer']);

//-- Cache is cleared to force update
LotgdCache::flush();

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/installer/default.twig', []));
