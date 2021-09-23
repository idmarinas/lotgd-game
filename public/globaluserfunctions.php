<?php

require_once 'common.php';
require_once 'lib/serverfunctions.class.php';

check_su_access(SU_MEGAUSER);

$textDomain = 'grotto_globaluserfunctions';

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addHeader('globaluserfunctions.category.actions');
\LotgdNavigation::addNav('globaluserfunctions.nav.reset', 'globaluserfunctions.php?op=dkpointreset');

$op = (string) \LotgdRequest::getQuery('op');
$params = [];

switch ($op)
{
    case 'dkpointreset':
        \LotgdNavigation::addHeader('globaluserfunctions.category.dragonpoints');
        \LotgdNavigation::addNav('globaluserfunctions.nav.reset.now', 'globaluserfunctions.php?op=dkpointresetnow');
    break;
    case 'dkpointresetnow':
        \LotgdKernel::get('lotgd_core.service.server_functions')->resetAllDragonkillPoints();

        $params['tpl'] = 'reset-now';
    break;
    default:
        $params['tpl'] = 'default';
    break;
}

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/globaluserfunctions.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
