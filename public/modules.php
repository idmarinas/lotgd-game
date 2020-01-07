<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';

check_su_access(SU_MANAGE_MODULES);

$textDomain = 'grotto-modules';

page_header('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

$params = [
    'textDomain' => $textDomain,
    'SU_EDIT_CONFIG' => ($session['user']['superuser'] & SU_EDIT_CONFIG)
];

$op = (string) \LotgdHttp::getQuery('op');
$module = (string) \LotgdHttp::getQuery('module');
$cat = (string) \LotgdHttp::getQuery('cat');

$repository = \Doctrine::getRepository('LotgdCore:Modules');

\LotgdNavigation::addHeader('modules.category.modules');

if ('mass' == $op)
{
    if (\LotgdHttp::getPost('activate'))
    {
        $op = 'activate';
    }
    elseif (\LotgdHttp::getPost('deactivate'))
    {
        $op = 'deactivate';
    }
    elseif (\LotgdHttp::getPost('uninstall'))
    {
        $op = 'uninstall';
    }
    elseif (\LotgdHttp::getPost('reinstall'))
    {
        $op = 'reinstall';
    }
    elseif (\LotgdHttp::getPost('install'))
    {
        $op = 'install';
    }

    $module = \LotgdHttp::getPost('module');
}

$theOp = $op;

$modules = $module;
if (! is_array($module) && $module)
{
    $modules = [$module];
}
elseif (! is_array($module) && ! $module)
{
    $modules = [];
}
reset($modules);

$params['messages'] = null;

foreach ($modules as $key => $module)
{
    $params['messages'][] = ['module.performing.'.$theOp, ['module' => $module]];

    if ('install' == $theOp)
    {
        if (! install_module($module))
        {
            \LotgdHttp::setQuery('cat', '');
            $cat = '';
            $params['messages'][] = ['module.fail.install'];
        }

        $op = '';
        \LotgdHttp::setQuery('op', '');

        LotgdCache::clearByPrefix('hook');
        LotgdCache::clearByPrefix('module-prepare');
    }
    elseif ('uninstall' == $theOp)
    {
        if (! uninstall_module($module))
        {
            \LotgdHttp::setQuery('cat', '');
            $cat = '';
            $params['messages'][] = ['module.fail.uninstall'];
        }

        $op = '';
        \LotgdHttp::setQuery('op', '');

        LotgdCache::clearByPrefix('hook');
        LotgdCache::clearByPrefix('module-prepare');

        LotgdCache::removeItem("inject-$module");
    }
    elseif ('activate' == $theOp)
    {
        activate_module($module);

        $op = '';
        \LotgdHttp::setQuery('op', '');

        LotgdCache::clearByPrefix('hook');
        LotgdCache::clearByPrefix('module-prepare');

        LotgdCache::removeItem("inject-$module");

        injectmodule($module, true);
    }
    elseif ('deactivate' == $theOp)
    {
        deactivate_module($module);

        $op = '';
        \LotgdHttp::setQuery('op', '');

        LotgdCache::removeItem("inject-$module");
        LotgdCache::clearByPrefix('module-prepare');
    }
    elseif ('reinstall' == $theOp)
    {
        $repository->reinstallModule($module);
        // We don't care about the return value here at all.

        $op = '';
        \LotgdHttp::setQuery('op', '');

        LotgdCache::removeItem("inject-$module");
        LotgdCache::clearByPrefix('hook');
        LotgdCache::clearByPrefix('module-prepare');

        injectmodule($module, true);
    }
}

$install_status = get_module_install_status();
$uninstmodules = $install_status['uninstalledmodules'];
$seencats = $install_status['installedcategories'];
$ucount = $install_status['uninstcount'];

ksort($seencats);
\LotgdNavigation::addNav('modules.nav.uninstalled', 'modules.php', [
    'params' => [
        'count' => $ucount
    ]
]);
\LotgdNavigation::addNav('modules.nav.installed', 'modules.php?op=installed', [
    'params' => [
        'count' => count($install_status['installedmodules'])
    ]
]);
reset($seencats);

foreach ($seencats as $category => $count)
{
    \LotgdNavigation::addNav('modules.nav.modules', 'modules.php?cat='.rawurlencode($category), [
        'params' => [
            'cat' => $category,
            'count' => $count
        ]
    ]);
}

if ('' == $op && 'installed' != $cat)
{
    $params['cat'] = $cat;

    if ($cat)
    {
        $params['modules'] = $repository->findBy(['category' => $cat], ['installdate' => 'DESC']);
    }
    else
    {
        $moduleinfo = [];

        if (count($uninstmodules))
        {
            $invalidmodule = [
                'version' => '',
                'author' => '',
                'category' => '',
                'download' => '',
                'invalid' => true,
            ];

            foreach ($uninstmodules as $key => $shortname)
            {
                //test if the file is a valid module or a lib file/whatever that got in, maybe even malcode that does not have module form
                $shortname = strtolower($shortname);
                $file = file_get_contents("modules/$shortname.php");

                //-- Here the files has neither do_hook nor getinfo, which means it won't execute as a module here --> block it + notify the admin who is the manage modules section
                $temp = array_merge($invalidmodule, [
                    'name' => $shortname.'.php '
                ]);

                if (false !== strpos($file, $shortname.'_getmoduleinfo')
                    && false !== strpos($file, $shortname.'_install')
                    && false !== strpos($file, $shortname.'_uninstall')
                ) {
                    $temp = get_module_info($shortname);
                }

                $temp['shortname'] = $shortname;

                $canInstall = true;

                if (isset($temp['requires']) && count($temp['requires']))
                {
                    reset($temp['requires']);

                    foreach ($temp['requires'] as $key => $val)
                    {
                        $info = explode('|', $val);
                        $temp['requiresCheck'][$key]['check'] = module_check_requirements([$key => $val]);
                        $temp['requiresCheck'][$key]['name'] = "$key {$info[0]} -- {$info[1]}`n";

                        if (! $temp['requiresCheck'][$key]['check'])
                        {
                            $canInstall = false;
                        }
                    }
                }

                $temp['canInstall'] = $canInstall;

                array_push($moduleinfo, $temp);
            }
        }

        $params['modules'] = $moduleinfo;
    }
}
elseif ('installed' == $op || 'installed' == $cat)
{
    $params['cat'] = 'installed';

    $params['modules'] = $repository->findBy([], ['active' => 'ASC', 'category' => 'ASC', 'installdate' => 'DESC']);
}

rawoutput(\LotgdTheme::renderLotgdTemplate('core/page/modules.twig', $params));

page_footer();
