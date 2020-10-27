<?php

require_once 'common.php';
require_once 'lib/serverfunctions.class.php';

check_su_access(SU_MEGAUSER);

$textDomain = 'grotto-globaluserfunctions';

page_header('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

\LotgdRequest::addHeader('globaluserfunctions.category.actions');
\LotgdRequest::addAdd('globaluserfunctions.nav.reset', 'globaluserfunctions.php?op=dkpointreset');

$op = (string) \LotgdRequest::getQuery('op');
$params = [];

switch ($op)
{
    case 'dkpointreset':
        \LotgdRequest::addHeader('globaluserfunctions.category.dragonpoints');
        \LotgdRequest::addNav('globaluserfunctions.nav.reset.now', 'globaluserfunctions.php?op=dkpointresetnow');
    break;
    case 'dkpointresetnow':
        \ServerFunctions::resetAllDragonkillPoints();

        $params['tpl'] = 'reset-now';
    break;
    default:
        $params['tpl'] = 'default';
    break;
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/globaluserfunctions.twig', $params));

page_footer();
