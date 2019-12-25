<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

checkday();

// Don't hook on to this text for your standard modules please, use "inn" instead.
// This hook is specifically to allow modules that do other inns to create ambience.
$result = modulehook('mercenarycamp-text-domain', ['textDomain' => 'page-mercenarycamp', 'textDomainNavigation' => 'navigation-mercenarycamp']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

$repository = \Doctrine::getRepository('LotgdCore:Companions');

\LotgdNavigation::addHeader('category.navigation');

page_header('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
];

$op = (string) \LotgdHttp::getQuery('op');
$skip = (int) \LotgdHttp::getQuery('skip');

if ('' == $op)
{
    $params['tpl'] = 'default';
    $params['showDescription'] = ! $skip;
    $params['companions'] = $repository->getMercenaryList($session['user']['location'], $session['user']['dragonkills']);

    \LotgdNavigation::addHeader('category.buynav');

    foreach ($params['companions'] as $row)
    {
        if ($row->getCompanioncostgold() || $row->getCompanioncostgems())
        {
            $link = '';
            $navParams = [
                'params' => [
                    'name' => $row->getName(),
                    'costGold' => $row->getCompanioncostgold(),
                    'costGems' => $row->getCompanioncostgems()
                ]
            ];

            if ($session['user']['gold'] >= $row->getCompanioncostgold() && $session['user']['gems'] >= $row->getCompanioncostgems() && ! isset($companions[$row->getName()]))
            {
                $link = "mercenarycamp.php?op=buy&id={$row->getCompanionid()}";
            }

            \LotgdNavigation::addNav('nav.companion.cost', $link, $navParams);
        }
        elseif (! isset($companions[$row->getName()]))
        {
            \LotgdNavigation::addNav($row->getName(), "mercenarycamp.php?op=buy&id={$row['companionid']}", ['translate' => false]);
        }
    }

    $params['companionWounds'] = healnav();
}
elseif ('heal' == $op)
{
    $params['tpl'] = 'heal';

    $name = stripslashes(rawurldecode(\LotgdHttp::getQuery('name')));

    $pointsToHeal = $companions[$name]['maxhitpoints'] - $companions[$name]['hitpoints'];
    $costToHeal = round(log($session['user']['level'] + 1) * ($pointsToHeal + 10) * 1.33);

    $params['companionHealed'] = false;
    if ($session['user']['gold'] >= $costToHeal)
    {
        $params['companionHealed'] = true;

        $companions[$name]['hitpoints'] = $companions[$name]['maxhitpoints'];
        $session['user']['companions'] = $companions;
        $session['user']['gold'] -= $costToHeal;

        debuglog("spent {$costToHeal} gold on healing a companion", false, false, 'healcompanion', $costToHeal);
    }

    $params['companionName'] = $companions[$name]['name'];

    $params['companionWounds'] = healnav();

    \LotgdNavigation::addHeader('category.navigation');
    \LotgdNavigation::addNav('nav.return', 'mercenarycamp.php?skip=1');
}
elseif ('buy' == $op)
{
    require_once 'lib/buffs.php';

    $params['tpl'] = 'buy';

    $companionId = (int) \LotgdHttp::getQuery('id');

    $entity = $repository->find($companionId);

    $params['companionHire'] = 'not.found';

    if ($entity)
    {
        $row = $repository->extractEntity($entity);

        $row['attack'] = $row['attack'] + $row['attackperlevel'] * $session['user']['level'];
        $row['defense'] = $row['defense'] + $row['defenseperlevel'] * $session['user']['level'];
        $row['maxhitpoints'] = $row['maxhitpoints'] + $row['maxhitpointsperlevel'] * $session['user']['level'];
        $row['hitpoints'] = $row['maxhitpoints'];

        $params['companionHire'] = apply_companion($row['name'], $row);

        if ($params['companionHire'])
        {
            $session['user']['gold'] -= $row['companioncostgold'];
            $session['user']['gems'] -= $row['companioncostgems'];

            debuglog("has spent {$row['companioncostgold']} gold and {$row['companioncostgems']} gems on hiring a mercenary ({$row['name']}).");
        }
    }

    \LotgdNavigation::addHeader('category.navigation');
    \LotgdNavigation::addNav('nav.return', 'mercenarycamp.php?skip=1');
}

\LotgdNavigation::addHeader('category.navigation');
\LotgdNavigation::villageNav();

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-mercenarycamp-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/mercenarycamp.twig', $params));

page_footer();

/**
 * Undocumented function.
 *
 * @return bool
 */
function healnav(): bool
{
    global $session, $companions;

    \LotgdNavigation::addHeader('category.companion.heal');

    $healable = false;

    foreach ($companions as $name => $companion)
    {
        if ($companion['cannotbehealed'] ?? false)
        {
            continue;
        }

        $pointsToHeal = $companion['maxhitpoints'] - $companion['hitpoints'];

        if ($pointsToHeal > 0)
        {
            $healable = true;
            $costToHeal = round(log($session['user']['level'] + 1) * ($pointsToHeal + 10) * 1.33);

            $nav = 'nav.companion.heal.not.have';
            $link = '';

            if ($session['user']['gold'] >= $costToHeal)
            {
                $nav = 'nav.companion.heal.have';
                $link = 'mercenarycamp.php?op=heal&name='.rawurlencode($name);
            }

            \LotgdNavigation::addNav($nav, $link, [
                'params' => [
                    'name' => $companion['name'],
                    'costGold' => $costToHeal
                ]
            ]);
        }
    }

    return $healable;
}
