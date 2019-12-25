<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/buffs.php';
require_once 'lib/sanitize.php';

// Don't hook on to this text for your standard modules please, use "stable" instead.
// This hook is specifically to allow modules that do other stables to create ambience.
$result = modulehook('stables-text-domain', ['textDomain' => 'page-stables', 'textDomainNavigation' => 'navigation-stables']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$playermount = getmount($session['user']['hashorse']);

$params = [
    'textDomain' => $textDomain,
    'barkeep' => getsetting('barkeep', '`tCedrik`0'),
    'userSex' => $session['user']['sex'],
    'mountName' => $playermount['mountname'] ?? ''
];

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

page_header('title', [], $textDomain);

\LotgdNavigation::addHeader('category.other');
\LotgdNavigation::villageNav();

$repaygold = 0;
$repaygems = 0;
$grubprice = 0;

if (! empty($playermount))
{
    $repaygold = round($playermount['mountcostgold'] * 2 / 3, 0);
    $repaygems = round($playermount['mountcostgems'] * 2 / 3, 0);
    $grubprice = round($session['user']['level'] * $playermount['mountfeedcost'], 0);
}
$confirm = 0;

$op = (string) \LotgdHttp::getQuery('op');
$mountId = (int) \LotgdHttp::getQuery('id');

$mountRepository = \Doctrine::getRepository('LotgdCore:Mounts');

if ('' == $op)
{
    checkday();

    $params['tpl'] = 'default';
}
elseif ('examine' == $op)
{
    $params['tpl'] = 'examine';
    $params['mount'] = $mountRepository->extractEntity($mountRepository->find($mountId));

    if ($params['mount'])
    {
        \LotgdNavigation::addHeaderNotl('New %s'.$params['mount']['mountname']);
        \LotgdNavigation::addNav('Buy this creature', "stables.php?op=buymount&id={$params['mount']['mountid']}");
    }
}
elseif ('buymount' == $op)
{
    $params['tpl'] = 'buymount';

    if ($session['user']['hashorse'])
    {
        \LotgdNavigation::addHeader('category.confirm.trade');
        \LotgdNavigaiton::addNav('nav.yes', "stables.php?op=confirmbuy&id={$mountId}");
        \LotgdNavigaiton::addNav('nav.no', 'stables.php');

        $confirm = 1;
    }
    else
    {
        $op = 'confirmbuy';
        \LotgdHttp::setQuery('op', $op);
    }
}

if ('confirmbuy' == $op)
{
    $params['tpl'] = 'confirmbuy';

    $mount = $mountRepository->extractEntity($mountRepository->find($mountId));

    if ($mount)
    {
        $params['mountBuyed'] = true;

        if (($session['user']['gold'] + $repaygold) < $mount['mountcostgold'] || ($session['user']['gems'] + $repaygems) < $mount['mountcostgems'])
        {
            $params['mountBuyed'] = false;
        }
        else
        {
            $params['mountReplace'] = false;
            $params['mountNameNew'] = $mount['mountname'];

            if ($session['user']['hashorse'])
            {
                $params['mountReplace'] = true;
            }

            $debugmount1 = $playermount['mountname'] ?? '';

            if ($debugmount1)
            {
                $debugmount1 = 'a '.$debugmount1;
            }
            $session['user']['hashorse'] = $mount['mountid'];
            $debugmount2 = $mount['mountname'];
            $goldcost = $repaygold - $mount['mountcostgold'];
            $session['user']['gold'] += $goldcost;
            $gemcost = $repaygems - $mount['mountcostgems'];
            $session['user']['gems'] += $gemcost;
            debuglog(($goldcost <= 0 ? 'spent ' : 'gained ').abs($goldcost).' gold and '.($gemcost <= 0 ? 'spent ' : 'gained ').abs($gemcost)." gems trading $debugmount1 for a new mount, a $debugmount2");

            $mount['mountbuff']['schema'] = $mount['mountbuff']['schema'] ?? 'mounts' ?: 'mounts';

            apply_buff('mount', $mount['mountbuff']);

            // Recalculate so the selling stuff works right
            $playermount = getmount($mount['mountid']);

            $repaygold = round($playermount['mountcostgold'] * 2 / 3, 0);
            $repaygems = round($playermount['mountcostgems'] * 2 / 3, 0);

            // Recalculate the special name as well.
            modulehook('stable-mount', []);
            modulehook('boughtmount');

            $grubprice = round($session['user']['level'] * $playermount['mountfeedcost'], 0);
        }
    }
}
elseif ('feed' == $op)
{
    $params['allowFeed'] = (int) getsetting('allowfeed', 0);
    $params['haveGold'] = ($session['user']['gold'] >= $grubprice);

    if ($params['haveGold'])
    {
        $mount['mountbuff']['schema'] = $mount['mountbuff']['schema'] ?? 'mounts' ?: 'mounts';
        $params['mountHungry'] = (isset($session['bufflist']['mount']) && $session['bufflist']['mount']['rounds'] == $mount['mountbuff']['rounds']);

        if ($params['mountHungry'])
        {
            $params['halfHungry'] = (isset($session['bufflist']['mount']) && $session['bufflist']['mount']['rounds'] > $mount['mountbuff']['rounds'] * .5);

            if ($params['halfHungry'])
            {
                $grubprice = round($grubprice / 2, 0);
            }

            $params['grubPrice'] = $grubprice;

            $session['user']['gold'] -= $grubprice;
            $session['user']['fedmount'] = 1;

            debuglog("spent $grubprice feeding their mount");

            apply_buff('mount', $mount['mountbuff']);
        }
    }
}
elseif ('sellmount' == $op)
{
    \LotgdNavigation::addHeader('category.confirm.sell');
    \LotgdNavigaiton::addNav('nav.yes', 'stables.php?op=confirmsell');
    \LotgdNavigaiton::addNav('nav.no', 'stables.php');

    $confirm = 1;
}
elseif ('confirmsell' == $op)
{
    $session['user']['gold'] += $repaygold;
    $session['user']['gems'] += $repaygems;
    $debugmount = $playermount['mountname'];
    debuglog("gained $repaygold gold and $repaygems gems selling their mount, a $debugmount");
    strip_buff('mount');
    $session['user']['hashorse'] = 0;
    modulehook('soldmount');

    $params['repayGold'] = $repaygold;
    $params['repayGems'] = $repaygems;

    $params['mountName'] = ($playermount['newname'] ? $playermount['newname'] : $playermount['mountname']);
}

$params['confirm'] = $confirm;

if (0 == $confirm)
{
    if ($session['user']['hashorse'] > 0)
    {
        $params['costGold'] = $repaygold;
        $params['costGems'] = $repaygems;
        $params['mountName'] = $playermount['mountname'];

        \LotgdNavigation::addHeaderNotl(\LotgdSanitize::fullSanitize($playermount['mountname']));

        \LotgdNavigation::addNav('nav.sell', 'stables.php?op=sellmount', [
            'params' => [
                'name' => $playermount['mountname']
            ]
        ]);

        if (getsetting('allowfeed', 0) && 0 == $session['user']['fedmount'])
        {
            \LotgdNavigation::addNav('nav.feed', 'stables.php?op=feed', [
                'params' => [
                    'name' => $playermount['mountname'],
                    'grubPrice' => $grubprice
                ]
            ]);
        }
    }

    $result = $mountRepository->getMountsByLocation($session['user']['location']);

    $category = '';

    foreach ($result as $row)
    {
        if ($category != $row->getMountcategory())
        {
            \LotgdNavigation::addHeaderNotl($row->getMountcategory());
            $category = $row->getMountcategory();
        }

        if ($row->getMountdkcost() <= $session['user']['dragonkills'])
        {
            \LotgdNavigation::addNav('nav.examine', "stables.php?op=examine&id={$row->getMountid()}", [
                'params' => [
                    'name' => $row->getMountname()
                ]
            ]);
        }
    }
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-stables-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/stables.twig', $params));

page_footer();
