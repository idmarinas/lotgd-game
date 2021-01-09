<?php

$setrace = \LotgdRequest::getQuery('setrace');

if ('' != $setrace)
{
    $vname = getsetting('villagename', LOCATION_FIELDS);
    //in case the module wants to reference it this way.
    $session['user']['race'] = $setrace;
    // Set the person to the main village/capital by default
    $session['user']['location'] = $vname;
    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_RACE_SET);
    modulehook('setrace');
    \LotgdNavigation::addNav('nav.continue', "newday.php?continue=1{$resline}");
}
else
{
    \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.choose.race', [], $textDomain));
    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_RACE_CHOOSE);
    modulehook('chooserace');
}

//-- Have navs
if (\LotgdNavigation::checkNavs())
{
    //-- Init page
    \LotgdResponse::pageStart('title.race.choose', [], $textDomain);
    //-- Finalize page
    \LotgdResponse::pageEnd();
}

$params['tpl'] = 'race';

//-- Init page
\LotgdResponse::pageStart('title.race.not', [], $textDomain);

$params['isAdmin'] = ($session['user']['superuser'] & (SU_MEGAUSER | SU_MANAGE_MODULES));

$session['user']['race'] = 'app_default'; // Default race

\LotgdNavigation::addNav('nav.continue', "newday.php?continue=1{$resline}");

//-- Finalize page
\LotgdResponse::pageEnd();
