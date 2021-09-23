<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';

check_su_access(SU_MANAGE_MODULES);

$textDomain = 'grotto_modules';

//_- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

$params = [
    'textDomain' => $textDomain,
    'SU_EDIT_CONFIG' => ($session['user']['superuser'] & SU_EDIT_CONFIG)
];

$op = (string) \LotgdRequest::getQuery('op');
$module = (string) \LotgdRequest::getQuery('module');
$cat = (string) \LotgdRequest::getQuery('cat');

$repository = \Doctrine::getRepository('LotgdCore:Modules');

\LotgdNavigation::addHeader('modules.category.status');

if ('mass' == $op)
{
    if (\LotgdRequest::getPost('activate'))
    {
        $op = 'activate';
    }
    elseif (\LotgdRequest::getPost('deactivate'))
    {
        $op = 'deactivate';
    }
    elseif (\LotgdRequest::getPost('uninstall'))
    {
        $op = 'uninstall';
    }
    elseif (\LotgdRequest::getPost('reinstall'))
    {
        $op = 'reinstall';
    }
    elseif (\LotgdRequest::getPost('install'))
    {
        $op = 'install';
    }

    $module = \LotgdRequest::getPost('module');
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

foreach ($modules as $module)
{
    $params['messages'][] = ['module.performing.'.$theOp, ['module' => $module]];

    if ('install' == $theOp)
    {
        if (! install_module($module))
        {
            \LotgdRequest::setQuery('cat', '');
            $cat = '';
            $params['messages'][] = ['module.fail.install'];
        }

        $op = '';
        \LotgdRequest::setQuery('op', '');
    }
    elseif ('uninstall' == $theOp)
    {
        if (! uninstall_module($module))
        {
            \LotgdRequest::setQuery('cat', '');
            $cat = '';
            $params['messages'][] = ['module.fail.uninstall'];
        }

        $op = '';
        \LotgdRequest::setQuery('op', '');
    }
    elseif ('activate' == $theOp)
    {
        activate_module($module);

        $op = '';
        \LotgdRequest::setQuery('op', '');

        injectmodule($module, true);
    }
    elseif ('deactivate' == $theOp)
    {
        deactivate_module($module);

        $op = '';
        \LotgdRequest::setQuery('op', '');
    }
    elseif ('reinstall' == $theOp)
    {
        $repository->reinstallModule($module);
        // We don't care about the return value here at all.

        $op = '';
        \LotgdRequest::setQuery('op', '');

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
\LotgdNavigation::addNav('modules.nav.deactivated', 'modules.php?op=deactivated', [
    'params' => [
        'count' => count($install_status['deactivedmodules'])
    ]
]);
\LotgdNavigation::addNav('modules.nav.activated', 'modules.php?op=activated', [
    'params' => [
        'count' => count($install_status['activedmodules'])
    ]
]);
reset($seencats);

\LotgdNavigation::addHeader('modules.category.modules');
foreach ($seencats as $category => $count)
{
    \LotgdNavigation::addNav('modules.nav.modules', 'modules.php?cat='.rawurlencode($category), [
        'params' => [
            'cat' => $category,
            'count' => $count
        ]
    ]);
}

if ('' == $op && 'installed' != $cat && 'deactivated' != $cat && 'activated' != $cat)
{
    $params['cat'] = $cat;

    if ($cat !== '' && $cat !== '0')
    {
        $params['modules'] = $repository->findBy(['category' => $cat], ['installdate' => 'DESC']);
    }
    else
    {
        $moduleinfo = [];

        if (count($uninstmodules) > 0)
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

                $moduleinfo[] = $temp;
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
elseif ('deactivated' == $op || 'deactivated' == $cat)
{
    $params['cat'] = 'deactivated';

    $params['modules'] = $repository->findBy(['active' => 0], ['category' => 'ASC', 'installdate' => 'DESC']);
}
elseif ('activated' == $op || 'activated' == $cat)
{
    $params['cat'] = 'activated';

    $params['modules'] = $repository->findBy(['active' => 1], ['category' => 'ASC', 'installdate' => 'DESC']);
}

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/modules.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
