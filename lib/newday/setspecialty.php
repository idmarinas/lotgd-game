<?php

$setspecialty = \LotgdRequest::getQuery('setspecialty');

if ('' != $setspecialty)
{
    $session['user']['specialty'] = $setspecialty;
    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_SPECIALTY_SET);
    modulehook('set-specialty');
    \LotgdNavigation::addNav('nav.continue', "newday.php?continue=1{$resline}");
}
else
{
    //-- Init page
    \LotgdResponse::pageStart('title.specialty.choose', [], $textDomain);
    \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.choose.specialty', [], $textDomain));
    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_SPECIALTY_CHOOSE);
    modulehook('choose-specialty');
}

//-- Have navs
if (\LotgdNavigation::checkNavs())
{
    //-- Finalize page
    \LotgdResponse::pageEnd();
}

$params['tpl'] = 'specialty';

//-- Init page
\LotgdResponse::pageStart('title.specialty.not', [], $textDomain);

$params['isAdmin'] = ($session['user']['superuser'] & (SU_MEGAUSER | SU_MANAGE_MODULES));

$session['user']['specialty'] = 'MP';
\LotgdNavigation::addNav('nav.continue', "newday.php?continue=1{$resline}");

//-- Finalize page
\LotgdResponse::pageEnd();
