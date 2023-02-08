<?php

// translator ready
// addnews ready
// mail ready

require_once 'common.php';

check_su_access(SU_EDIT_CONFIG);

$textDomain = 'grotto_configuration';

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

$save         = (string) LotgdRequest::getQuery('save');
$type_setting = (string) LotgdRequest::getQuery('setting');

$params = [
    'textDomain' => $textDomain,
];

LotgdNavigation::superuserGrottoNav();

LotgdNavigation::addHeader('configuration.category.settings');
LotgdNavigation::addNav('configuration.nav.standard', 'configuration.php');
LotgdNavigation::addNav('configuration.nav.cache', 'configuration.php?setting=cache');
LotgdNavigation::addNav('configuration.nav.cronjob', 'configuration.php?setting=cronjob');

LotgdNavigation::addNavAllow(LotgdRequest::getServer('REQUEST_URI'));

module_editor_navs('settings', 'configuration.php?setting=module&module=');

switch ($type_setting)
{
    case 'cache':
        $params['tpl'] = 'cache';

    break;
    case 'cronjob':
        $params['tpl'] = 'cronjob';

        require_once 'lib/configuration/cronjob.php';

    break;
    case 'module':
        $params['tpl'] = 'module';

        require_once 'lib/configuration/module.php';

    break;
    case 'default':
    default:
        $params['tpl'] = 'default';

        require_once 'lib/configuration/default.php';

    break;
}

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/configuration.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();
