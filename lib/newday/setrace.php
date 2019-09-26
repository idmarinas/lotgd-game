<?php

$setrace = \LotgdHttp::getQuery('setrace');

if ('' != $setrace)
{
    $vname = getsetting('villagename', LOCATION_FIELDS);
    //in case the module wants to reference it this way.
    $session['user']['race'] = $setrace;
    // Set the person to the main village/capital by default
    $session['user']['location'] = $vname;
    modulehook('setrace');
    \LotgdNavigation::addNav('nav.continue', "newday.php?continue=1$resline");
}
else
{
    \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.choose.race', [], $textDomain));
    modulehook('chooserace');
}

//-- Have navs
if (\LotgdNavigation::checkNavs())
{
    page_header('title.race.choose', [], $textDomain);
    page_footer();
}

clearoutput();

page_header('title.race.not', [], $textDomain);

$params['isAdmin'] = ($session['user']['superuser'] & (SU_MEGAUSER | SU_MANAGE_MODULES));

$session['user']['race'] = 'Human';

\LotgdNavigation::addNav('nav.continue', "newday.php?continue=1$resline");

page_footer();
