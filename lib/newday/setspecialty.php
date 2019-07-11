<?php

$setspecialty = \LotgdHttp::getQuery('setspecialty');

if ('' != $setspecialty)
{
    $session['user']['specialty'] = $setspecialty;
    modulehook('set-specialty');
    \LotgdNavigation::addNav('nav.continue', "newday.php?continue=1$resline");
}
else
{
    page_header('title.specialty.choose', [], $textDomain);
    \LotgdFlashMessage::addInfoMessage(\LotgdTranslator::t('flash.message.choose.specialty', [], $textDomain));
    modulehook('choose-specialty');
}

//-- Have navs
if (\LotgdNavigation::checkNavs())
{
    page_footer();
}

$params['tpl'] = 'specialty';

clearoutput();
page_header('title.specialty.not', [], $textDomain);

$params['isAdmin'] = ($session['user']['superuser'] & (SU_MEGAUSER | SU_MANAGE_MODULES));

$session['user']['specialty'] = 'MP';
\LotgdNavigation::addNav('nav.continue', "newday.php?continue=1$resline");

page_footer();
