<?php

// translator ready
// addnews ready
// mail ready

require_once 'common.php';
require_once 'lib/showform.php';
require_once 'lib/gamelog.php';

check_su_access(SU_EDIT_CONFIG);

$textDomain = 'grotto-configuration';

page_header('title', [], $textDomain);

$save = (string) \LotgdHttp::getQuery('save');
$type_setting = (string) \LotgdHttp::getQuery('setting');

$params = [];

\LotgdNavigation::superuserGrottoNav();
\LotgdNavigation::addNav('Module Manager', 'modules.php');

\LotgdNavigation::addHeader('configuration.category.settings');
\LotgdNavigation::addNav('configuration.nav.standard', 'configuration.php');
\LotgdNavigation::addNav('configuration.nav.cache', 'configuration.php?setting=cache');
\LotgdNavigation::addNav('configuration.nav.cronjob', 'configuration.php?setting=cronjob');

\LotgdNavigation::addNavAllow(\LotgdHttp::getServer('REQUEST_URI'));

module_editor_navs('settings', 'configuration.php?setting=module&module=');

switch ($type_setting)
{
    case 'cache':
        $params['tpl'] = 'cache';

        require_once 'lib/configuration/cache.php';
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

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/configuration.twig', $params));

page_footer();
