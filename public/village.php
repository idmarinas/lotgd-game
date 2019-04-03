<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/events.php';
require_once 'lib/experience.php';

tlschema('village');

// See if the user is in a valid location and if not, put them back to
// a place which is valid
$valid_loc = [];
$vname = getsetting('villagename', LOCATION_FIELDS);
$iname = getsetting('innname', LOCATION_INN);
$valid_loc[$vname] = 'village';
$valid_loc = modulehook('validlocation', $valid_loc);
$params = [];

if (! isset($valid_loc[$session['user']['location']]))
{
    $session['user']['location'] = $vname;
}

//-- Newest player in realm
$newestname = '';
$newestplayer = (int) getsetting('newestplayer', 0);
$newestname = (string) getsetting('newestplayername', '');

$newtext = 'newestOther';
if ($newestplayer == $session['user']['acctid'])
{
    $newtext = 'newestPlayer';
    $newestname = $session['user']['name'];
}
elseif (! $newestname && $newestplayer)
{
    $characterRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);
    $newestname = $characterRepository->getCharacterNameFromAcctId($newestplayer) ?: 'Unknown';
    savesetting('newestplayername', $newestname);
}

$origtexts = [
    //-- Comentary
    'section' => 'village',//-- Deprecated only for legacy removed in 4.1.0
    'talk' => 'talk',//-- Deprecated only for legacy removed in 4.1.0
    'sayline' => 'says',//-- Deprecated only for legacy removed in 4.1.0
    'commentary' => [
        'section' => 'village', //-- This is name of section for comentaries
        'talk' => 'commentary.talk', //-- Key for translation talk
        'sayLine' => 'commentary.sayLine', //-- Key for translation say line
        'button' => 'commentary.button' //-- Key for translation for button add
    ],

    //-- Newest player
    'newest' => $newtext,
    'newestplayer' => $newestname,
    'newestid' => $newestplayer,

    //-- Village text
    'text' => [
        [//-- Paragraph
            'text', //-- Translation key
            ['village' => $vname] //-- Params for translation
        ]
    ],
    'clock' => 'clock',
    'title' => [
        'title', //-- Translation key
        ['village' => $vname] //-- Params for translation
    ],

    //-- Navigation headers
    'gatenav' => 'headers.gate',
    'fields' => 'headers.fields',
    'fightnav' => 'headers.fight',
    'marketnav' => 'headers.market',
    'tavernnav' => 'headers.tavern',
    'industrialnav' => 'headers.industrial',
    'infonav' => 'headers.info',
    'othernav' => 'headers.other',

    //-- Navigation menus
    'forest' => 'navs.forest',
    'pvp' => 'navs.pvp',
    'logout' => 'navs.logout',
    'innname' => 'navs.innname',
    'stablename' => 'navs.stablename',
    'mercenarycamp' => 'navs.mercenarycamp',
    'train' => 'navs.train',
    'lodge' => 'navs.lodge',
    'armorshop' => 'navs.armorshop',
    'weaponshop' => 'navs.weaponshop',
    'bank' => 'navs.bank',
    'gypsy' => 'navs.gypsy',
    'pavilion' => 'navs.pavilion',
    'gardens' => 'navs.gardens',
    'rock' => 'navs.rock',
    'clan' => 'navs.clan',
    'faq' => 'navs.faq',
    'news' => 'navs.news',
    'list' => 'navs.list',
    'hof' => 'navs.hof',
    'account' => 'navs.account',
    'prefs' => 'navs.prefs',
    'referral' => 'navs.referral',
];
$schemas = [
    //-- Comentary
    'section' => 'page-village',//-- Deprecated only for legacy removed in 4.1.0
    'talk' => 'page-village',//-- Deprecated only for legacy removed in 4.1.0
    'sayline' => 'page-village',//-- Deprecated only for legacy removed in 4.1.0
    //-- All comentary options have the same text domain
    'commentary' => 'page-village',

    //-- Newest player
    'newest' => 'page-village',
    'newestplayer' => 'page-village',
    'newestid' => 'page-village',

    //- Village text
    'text' => 'page-village',
    'clock' => 'page-village',
    'title' => 'page-village',

    //-- Navigation headers
    'gatenav' => 'navigation-village',
    'fields' => 'navigation-village',
    'fightnav' => 'navigation-village',
    'marketnav' => 'navigation-village',
    'tavernnav' => 'navigation-village',
    'industrialnav' => 'navigation-village',
    'infonav' => 'navigation-village',
    'othernav' => 'navigation-village',

    //-- Navigation menus
    'forest' => 'navigation-village',
    'pvp' => 'navigation-village',
    'logout' => 'navigation-village',
    'innname' => 'navigation-village',
    'stablename' => 'navigation-village',
    'mercenarycamp' => 'navigation-village',
    'train' => 'navigation-village',
    'lodge' => 'navigation-village',
    'armorshop' => 'navigation-village',
    'weaponshop' => 'navigation-village',
    'bank' => 'navigation-village',
    'gypsy' => 'navigation-village',
    'pavilion' => 'navigation-village',
    'gardens' => 'navigation-village',
    'rock' => 'navigation-village',
    'clan' => 'navigation-village',
    'faq' => 'navigation-village',
    'news' => 'navigation-village',
    'list' => 'navigation-village',
    'hof' => 'navigation-village',
    'account' => 'navigation-village',
    'prefs' => 'navigation-village',
    'referral' => 'navigation-village',
];
// Now store the schemas
$origtexts['schemas'] = $schemas;

// Don't hook on to this text for your standard modules please, use "village" instead.
// This hook is specifically to allow modules that do other villages to create ambience.
$texts = modulehook('villagetext', $origtexts);
//and now a special hook for the village
$texts = modulehook("villagetext-{$session['user']['location']}", $texts);
$schemas = $texts['schemas'];
unset($texts['schemas']);

$params['texts'] = $texts;
$params['schemas'] = $schemas;

$title = $texts['title'];
$title[] = $schemas['title'];
call_user_func_array('page_header', $title);

$skipvillagedesc = handle_event('village');
checkday();

if (1 == $session['user']['slaydragon'])
{
    $session['user']['slaydragon'] = 0;
}

if (! $session['user']['alive'])
{
    return redirect('shades.php');
}

if (getsetting('automaster', 1) && 1 != $session['user']['seenmaster'])
{
    //masters hunt down truant students
    $level = $session['user']['level'] + 1;
    $dks = $session['user']['dragonkills'];
    $expreqd = exp_for_next_level($level, $dks);

    if ($session['user']['experience'] > $expreqd && $session['user']['level'] < getsetting('maxlevel', 15))
    {
        return redirect('train.php?op=autochallenge');
    }
}

$op = \LotgdHttp::getQuery('op');
$com = \LotgdHttp::getQuery('commentPage');
$commenting = \LotgdHttp::getQuery('commenting');
$comment = \LotgdHttp::getPost('comment');
// Don't give people a chance at a special event if they are just browsing
// the commentary (or talking) or dealing with any of the hooks in the village.
// The '1' should really be sysadmin customizable.
if (! $op && '' == $com && ! $comment && ! $commenting && 0 != module_events('village', getsetting('villagechance', 0)))
{
    if (\LotgdNavigation::checkNavs())
    {
        page_footer();
    }
    else
    {
        // Reset the special for good.
        $session['user']['specialinc'] = '';
        $session['user']['specialmisc'] = '';
        $skipvillagedesc = true;
        $op = '';
        \LotgdHttp::setQuery('op', '');
    }
}

//-- City gates
\LotgdNavigation::addHeader($texts['gatenav'], ['textDomain' => $schemas['gatenav']]);
\LotgdNavigation::addNav($texts['forest'], 'forest.php', ['textDomain' => $schemas['forest']]);

if (getsetting('pvp', 1))
{
    \LotgdNavigation::addNav($texts['pvp'], 'pvp.php', ['textDomain' => $schemas['pvp']]);
}

//-- Fields
\LotgdNavigation::addHeader($texts['fields'], ['textDomain' => $schemas['fields']]);
\LotgdNavigation::addNav($texts['logout'], 'login.php?op=logout', ['textDomain' => $schemas['logout']]);

if (getsetting('enablecompanions', true))
{
    \LotgdNavigation::addNav($texts['mercenarycamp'], 'mercenarycamp.php', ['textDomain' => $schemas['mercenarycamp']]);
}

//-- Fight street
\LotgdNavigation::addHeader($texts['fightnav'], ['textDomain' => $schemas['fightnav']]);
\LotgdNavigation::addNav($texts['train'], 'train.php', ['textDomain' => $schemas['train']]);

if (file_exists('lodge.php'))
{
    \LotgdNavigation::addNav($texts['lodge'], 'lodge.php', ['textDomain' => $schemas['lodge']]);
}

//-- Market street
\LotgdNavigation::addHeader($texts['marketnav'], ['textDomain' => $schemas['marketnav']]);
\LotgdNavigation::addNav($texts['weaponshop'], 'weapons.php', ['textDomain' => $schemas['weaponshop']]);
\LotgdNavigation::addNav($texts['armorshop'], 'armor.php', ['textDomain' => $schemas['armorshop']]);
\LotgdNavigation::addNav($texts['bank'], 'bank.php', ['textDomain' => $schemas['bank']]);
\LotgdNavigation::addNav($texts['gypsy'], 'gypsy.php', ['textDomain' => $schemas['gypsy']]);

if (1 == getsetting('betaperplayer', 1) && file_exists('pavilion.php'))
{
    \LotgdNavigation::addNav($texts['pavilion'], 'pavilion.php', ['textDomain' => $schemas['pavilion']]);
}

//-- Industrial street
\LotgdNavigation::addHeader($texts['industrialnav'], ['textDomain' => $schemas['industrialnav']]);

//-- Tavern street
\LotgdNavigation::addHeader($texts['tavernnav'], ['textDomain' => $schemas['tavernnav']]);
\LotgdNavigation::addNav($texts['innname'], 'inn.php', ['textDomain' => $schemas['innname'], 'params' => ['inn' => $iname]]);
\LotgdNavigation::addNav($texts['stablename'], 'stables.php', ['textDomain' => $schemas['stablename']]);
\LotgdNavigation::addNav($texts['gardens'], 'gardens.php', ['textDomain' => $schemas['gardens']]);
\LotgdNavigation::addNav($texts['rock'], 'rock.php', ['textDomain' => $schemas['rock']]);

if (getsetting('allowclans', 1))
{
    \LotgdNavigation::addnav($texts['clan'], 'rock.php', ['textDomain' => $schemas['clan']]);
}

//-- Info street
\LotgdNavigation::addHeader($texts['infonav'], ['textDomain' => $schemas['infonav']]);
\LotgdNavigation::addNav($texts['faq'], 'petition.php?op=faq', [
    'textDomain' => $schemas['faq'],
    'attributes' => [
        'data-force' => 'true',
        'onclick' => 'Lotgd.embed(this)'
    ]
]);
\LotgdNavigation::addNav($texts['news'], 'news.php', ['textDomain' => $schemas['news']]);
\LotgdNavigation::addNav($texts['list'], 'list.php', ['textDomain' => $schemas['list']]);
\LotgdNavigation::addNav($texts['hof'], 'hof.php', ['textDomain' => $schemas['hof']]);

//-- Other navs
\LotgdNavigation::addHeader($texts['othernav'], ['textDomain' => $schemas['othernav']]);
\LotgdNavigation::addNav($texts['account'], 'account.php', ['textDomain' => $schemas['account']]);
\LotgdNavigation::addNav($texts['prefs'], 'prefs.php', ['textDomain' => $schemas['prefs']]);

if (! file_exists('lodge.php'))
{
    \LotgdNavigation::addNav($texts['referral'], 'referral.php', ['textDomain' => $schemas['referral']]);
}

//-- Superuser menu
\LotgdNavigation::superuser();

//special hook for all villages... saves queries...
modulehook('village');
modulehook("village-{$session['user']['location']}");

$params['showVillageDesc'] = ! $skipvillagedesc; //-- Show or not village description
$params['SU_EDIT_USERS'] = $session['user']['superuser'] & SU_EDIT_USERS;
$params['blockCommentArea'] = false; //-- Show or not comment area

//-- This is only for params not use for other purpose
$params = modulehook('page-village-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/village.twig', $params));

module_display_events('village', 'village.php');

page_footer();
