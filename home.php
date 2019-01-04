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
        $_COOKIE['template'] = $skin;
    }
}

define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

$session['loggedin'] = $session['loggedin'] ?? false;

if ($session['loggedin'])
{
    return redirect('badnav.php');
}

tlschema('home');

$op = httpget('op');

page_header();
output("`cWelcome to Legend of the Green Dragon, a browser based role playing game, based on Seth Able's Legend of the Red Dragon.`n");

if (getsetting('homecurtime', 1))
{
    output('`@The current time in %s is `%%s`@.`0`n', getsetting('villagename', LOCATION_FIELDS), getgametime());
}

if (getsetting('homenewdaytime', 1))
{
    $secstonewday = secondstonextgameday();
    output('`@Next new game day in: `$%s (real time)`0`n`n',
            date('G\\'.translate_inline('h', 'datetime').', i\\'.translate_inline('m', 'datetime').', s\\'.translate_inline('s', 'datetime'),
                $secstonewday));
}

if (getsetting('homenewestplayer', 1))
{
    $name = '';
    $newplayer = getsetting('newestplayer', '');

    $name = $newplayer;
    if (0 != $newplayer)
    {
        $sql = 'SELECT name FROM '.DB::prefix('accounts')." WHERE acctid='$newplayer'";
        $result = DB::query($sql);
        $row = DB::fetch_assoc($result);
        $name = $row['name'];
    }

    if ('' != $name)
    {
        output('`QThe newest resident of the realm is:`0 `&%s`0`n`n', $name, true);
    }
}

clearnav();
addnav('New to LoGD?');
addnav('Create a character', 'create.php');
addnav('Game Functions');
addnav('Forgotten Password', 'create.php?op=forgot');
addnav('List Warriors', 'list.php');
addnav('Daily News', 'news.php');
addnav('Other Info');
addnav('About LoGD', 'about.php');
addnav('Game Setup Info', 'about.php?op=setup');
addnav('LoGD Net', 'logdnet.php?op=list');

modulehook('index', []);

if (abs(getsetting('OnlineCountLast', 0) - strtotime('now')) > 60)
{
    $sql = 'SELECT count(acctid) as onlinecount FROM '.DB::prefix('accounts')." WHERE locked=0 AND loggedin=1 AND laston>'".date('Y-m-d H:i:s', strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds'))."'";
    $result = DB::query($sql);
    $onlinecount = DB::fetch_assoc($result);
    savesetting('OnlineCount', $onlinecount['onlinecount']);
    savesetting('OnlineCountLast', strtotime('now'));
}

$onlinecount = getsetting('OnlineCount', 0);

modulehook('hometext', []);

if (! isset($session['message']))
{
    $session['message'] = '';
}

if ($onlinecount < getsetting('maxonline', 0) || 0 == getsetting('maxonline', 0))
{
    if ('timeout' == $op)
    {
        $session['message'] .= translate_inline(' Your session has timed out, you must log in again.`n');
    }

    if (! isset($_COOKIE['lgi']))
    {
        $session['message'] .= translate_inline('It appears that you may be blocking cookies from this site.  At least session cookies must be enabled in order to use this site.`n');
        $session['message'] .= translate_inline("`b`#If you are not sure what cookies are, please <a href='http://en.wikipedia.org/wiki/WWW_browser_cookie'>read this article</a> about them, and how to enable them.´b`n");
    }

    if (isset($session['message']) && $session['message'] > '')
    {
        output_notl('`b`$%s´b`n', $session['message'], true);
    }

    $formLogin = LotgdTheme::renderThemeTemplate('pages/home/login.twig', [
        'text' => translate_inline('Enter your name and password to enter the realm.`n'),
        'username' => translate_inline('Username'),
        'password' => translate_inline('Password'),
        'button' => translate_inline('Log in')
    ]);
    rawoutput("<form action='login.php' method='POST' onSubmit=\"md5pass();\">".$formLogin.'</form>');

    rawoutput("<script language='JavaScript' src='resources/md5.js'></script>");
    rawoutput("<script language='JavaScript'>
    <!--
    function md5pass(){
        //encode passwords before submission to protect them even from network sniffing attacks.
        var passbox = document.getElementById('password');
        if (passbox.value.substring(0, 5) != '!md5!') {
            passbox.value = '!md5!' + hex_md5(passbox.value);
        }
    }
    //-->
    </script>");
    addnav('', 'login.php');
}
else
{
    output('`$`bServer full!´b`n`^Please wait until some users have logged out.`n`n`0');

    if ('timeout' == $op)
    {
        $session['message'] .= translate_inline(' Your session has timed out, you must log in again.`n');
    }

    if (! isset($_COOKIE['lgi']))
    {
        $session['message'] .= translate_inline('It appears that you may be blocking cookies from this site. At least session cookies must be enabled in order to use this site.`n');
        $session['message'] .= translate_inline("`b`#If you are not sure what cookies are, please <a href='http://en.wikipedia.org/wiki/WWW_browser_cookie'>read this article</a> about them, and how to enable them.´b`n");
    }

    if (isset($session['message']) && $session['message'] > '')
    {
        output('`b`$%s´b`n', $session['message'], true);
    }
    rawoutput(LotgdTheme::renderThemeTemplate('pages/home/loginfull.twig', ['text' => translate_inline('Server Full!')]));
}
output_notl('´c');

modulehook('homemiddle', []);

$msg = getsetting('loginbanner', '*BETA* This is a BETA of this website, things are likely to change now and again, as it is under active development *BETA*');
output_notl('`n`c`b`&%s`0´b´c`n', $msg);
$session['message'] = '';
output('`c`2Game server running version: `@%s`0´c', \Lotgd\Core\Application::VERSION);

if (getsetting('homeskinselect', 1))
{
    require_once 'lib/showform.php';

    $prefs['template'] = (isset($_COOKIE['template']) ? $_COOKIE['template'] : '');

    if ('' == $prefs['template'])
    {
        $prefs['template'] = getsetting('defaultskin', 'jade.htm');
    }
    $form = ['template' => 'Choose a different display skin:,theme'];

    rawoutput("<br><form action='home.php' method='POST'>");
    rawoutput('<table class="ui very basic centered collapsing table"><tr><td><div class="ui form"><div class="inline fields"><div class="field">');
    lotgd_showform($form, $prefs, true);
    $submit = translate_inline('Choose');
    rawoutput("</div><div class='field'><input type='submit' class='ui button' value='$submit'></div>");
    rawoutput('</div></div></td></tr></table></form>');
}
page_footer();
