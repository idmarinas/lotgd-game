<?php

// translator ready
// addnews ready
// mail ready

if (isset($_POST['template']))
{
    $skin = $_POST['template'];

    if ($skin > '')
    {
        setcookie('template', $skin, strtotime('+45 days'));
        httpSetCookie('template', $skin);
    }
}

define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

if ($session['loggedin'] ?? false)
{
    return redirect('badnav.php');
}

$op = httpget('op');

page_header(LotgdTranslator::t('title', [], 'page-home'));

LotgdNavigation::addHeader('home.category.new');
LotgdNavigation::addNav('home.nav.create', 'create.php');

LotgdNavigation::addHeader('home.category.func');
LotgdNavigation::addNav('home.nav.forgot', 'create.php?op=forgot');
LotgdNavigation::addNav('home.nav.list', 'list.php');
LotgdNavigation::addNav('home.nav.news', 'news.php');

LotgdNavigation::addHeader('home.category.other');
LotgdNavigation::addNav('home.nav.about', 'about.php');
LotgdNavigation::addNav('home.nav.setup', 'about.php?op=setup');
LotgdNavigation::addNav('home.nav.net', 'logdnet.php?op=list');

//-- Parameters to be passed to the template
$params = [
    'villagename' => getsetting('villagename', LOCATION_FIELDS),
];

if (getsetting('homecurtime', 1))
{
    $params['gameclock'] = getgametime();
}

if (getsetting('homenewdaytime', 1))
{
    $params['newdaytimer'] = secondstonextgameday();
}

//-- Get newest player name if show in home page
if (getsetting('homenewestplayer', 1))
{
    $name = (string) getsetting('newestPlayerName', '');
    $old = (int) getsetting('newestPlayerOld', 0);
    $new = (int) getsetting('newestplayer', 0);

    if (0 != $new && $old != $new)
    {
        $character = Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);
        $name = $character->getCharacterNameFromAcctId($new);
        savesetting('newestPlayerName', $name);
        savesetting('newestPlayerOld', $new);
    }

    $params['newestplayer'] = $name;
}

$results = modulehook('index', ['messages' => []]);
if(count($results['messages']))
{
    $params['hookIndex'] = $results['messages'];
}

if (abs(getsetting('OnlineCountLast', 0) - strtotime('now')) > 60)
{
    $account = Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);

    savesetting('OnlineCount', $account->getCountAcctsOnline((int) getsetting('LOGINTIMEOUT', 900)));
    savesetting('OnlineCountLast', strtotime('now'));
}

$params['OnlineCount'] = getsetting('OnlineCount', 0);

$results = modulehook('hometext', ['messages' => []]);
if(count($results['messages']))
{
    $params['hookHomeText'] = $results['messages'];
}

if ('timeout' == $op)
{
    LotgdFlashMessages::addWarningMessage(LotgdTranslator::t('session.timeout', [], 'app-default'));
}
if (! LotgdHttp::getCookie('lgi'))
{
    LotgdFlashMessages::addWarningMessage(LotgdTranslator::t('session.cookies.unactive', [], 'app-default'));
    LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('session.cookies.info', [], 'app-default'));
}

$params['serverFull'] = true;
if ($params['OnlineCount'] < getsetting('maxonline', 0) || 0 == getsetting('maxonline', 0))
{
    $params['serverFull'] = false;
}

$results = modulehook('homemiddle', ['messages' => []]);
if(count($results['messages']))
{
    $params['hookMomeMiddle'] = $results['messages'];
}

//-- By default not have banner are
$params['loginBanner'] = getsetting('loginbanner');
//-- Version of the game the server is running
$params['serverVersion'] = \Lotgd\Core\Application::VERSION;

$params['selectSkin'] = false;
if (getsetting('homeskinselect', 1))
{
    require_once 'lib/showform.php';

    $prefs['template'] = LotgdHttp::getCookie('template') ?: '';

    if ('' == $prefs['template'])
    {
        $prefs['template'] = getsetting('defaultskin', 'jade.htm');
    }
    $form = ['template' => 'Choose a different display skin:,theme'];

    $params['selectSkin'] = lotgd_showform($form, $prefs, true, false, false);
}

$params = modulehook('page-home-tpl-params', $params);
rawoutput(LotgdTheme::renderThemeTemplate('pages/home.twig', $params));

page_footer();
