<?php

// Decline static file requests back to the PHP built-in webserver
if ('cli-server' === php_sapi_name() && is_file(__DIR__.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)))
{
    return false;
}

// translator ready
// addnews ready
// mail ready

$pagestarttime = microtime(true);

// **** NOTICE ****
// This series of scripts (collectively known as Legend of the Green Dragon
// or LotGD) is copyright as per below.
// You are prohibited by law from removing or altering this copyright
// information in any fashion except as follows:
//		if you have added functionality to the code, you may append your
// 		name at the end indicating which parts are copyright by you.
// Eg:
// Copyright 2002-2004, Game: Eric Stevens & JT Traub, modified by Your Name
$copyright = 'Game Design and Code: Copyright &copy; 2002-2005, Eric Stevens & JT Traub, &copy; 2006-2007, Dragonprime Development Team, &copy; 2015-2017 IDMarinas remodelling and enhancing';
// **** NOTICE ****
// This series of scripts (collectively known as Legend of the Green Dragon
// or LotGD) is copyright as per above.   Read the above paragraph for
// instructions regarding this copyright notice.

// **** NOTICE ****
// This series of scripts (collectively known as Legend of the Green Dragon
// or LotGD) is licensed according to the Creating Commons Attribution
// Non-commercial Share-alike license.  The terms of this license must be
// followed for you to legally use or distribute this software.   This
// license must be used on the distribution of any works derived from this
// work.  This license text may not be removed nor altered in any way.
// Please see the file LICENSE for a full textual description of the license.
$license = "\n<!-- Creative Commons License -->\n<a rel='license' href='http://creativecommons.org/licenses/by-nc-sa/2.0/' target='_blank'><img clear='right' align='left' alt='Creative Commons License' border='0' src='images/somerights20.gif' /></a>\nThis work is licensed under a <a rel='license' href='http://creativecommons.org/licenses/by-nc-sa/2.0/' target='_blank'>Creative Commons License</a>.<br />\n<!-- /Creative Commons License -->\n<!--\n  <rdf:RDF xmlns='http://web.resource.org/cc/' xmlns:dc='http://purl.org/dc/elements/1.1/' xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#'>\n	<Work rdf:about=''>\n	  <dc:type rdf:resource='http://purl.org/dc/dcmitype/Interactive' />\n	  <license rdf:resource='http://creativecommons.org/licenses/by-nc-sa/2.0/' />\n	</Work>\n	<License rdf:about='http://creativecommons.org/licenses/by-nc-sa/2.0/'>\n	  <permits rdf:resource='http://web.resource.org/cc/Reproduction' />\n	  <permits rdf:resource='http://web.resource.org/cc/Distribution' />\n	  <requires rdf:resource='http://web.resource.org/cc/Notice' />\n	  <requires rdf:resource='http://web.resource.org/cc/Attribution' />\n	  <prohibits rdf:resource='http://web.resource.org/cc/CommercialUse' />\n	  <permits rdf:resource='http://web.resource.org/cc/DerivativeWorks' />\n	  <requires rdf:resource='http://web.resource.org/cc/ShareAlike' />\n	</License>\n  </rdf:RDF>\n-->\n";

// **** NOTICE *****
// This series of scripts (collectively known as Legend of the Green Dragon
// or LotGD) is licensed according to the Creating Commons Attribution
// Non-commercial Share-alike license.  The terms of this license must be
// followed for you to legally use or distribute this software.   This
// license must be used on the distribution of any works derived from this
// work.  This license text may not be removed nor altered in any way.
// Please see the file LICENSE for a full textual description of the license.

$logd_version = '2.7.0 IDMarinas Edition';

session_start();

$session = &$_SESSION['session'];

$session['user']['gentime'] = $session['user']['gentime'] ?? 0;
$session['user']['gentimecount'] = $session['user']['gentimecount'] ?? 0;
$session['user']['gensize'] = $session['user']['gensize'] ?? 0;
$session['user']['acctid'] = $session['user']['acctid'] ?? 0;
$session['counter'] = $session['counter'] ?? 0;

$session['counter']++;

require_once 'vendor/autoload.php'; //-- Autoload class for new options of game
// Include some commonly needed and useful routines
require_once 'lib/output.php';
require_once 'lib/nav.php';
require_once 'lib/dbwrapper.php';
require_once 'lib/holiday_texts.php';
require_once 'lib/constants.php';
require_once 'lib/datacache.php';
require_once 'lib/modules.php';
require_once 'lib/http.php';
require_once 'lib/e_rand.php';
require_once 'lib/buffs.php';
require_once 'lib/pageparts.php';
require_once 'lib/sanitize.php';
require_once 'lib/tempstat.php';
require_once 'lib/su_access.php';
require_once 'lib/datetime.php';
require_once 'lib/translator.php';
require_once 'lib/playerfunctions.php';

// Set some constant defaults in case they weren't set before the inclusion of
// common.php
if (! defined('OVERRIDE_FORCED_NAV'))
{
    define('OVERRIDE_FORCED_NAV', false);
}

if (! defined('ALLOW_ANONYMOUS'))
{
    define('ALLOW_ANONYMOUS', false);
}

//Initialize variables required for this page

require_once 'lib/settings.php';
require_once 'lib/redirect.php';
require_once 'lib/censor.php';
require_once 'lib/saveuser.php';
require_once 'lib/arrayutil.php';
require_once 'lib/addnews.php';
require_once 'lib/mounts.php';
require_once 'lib/debuglog.php';
require_once 'lib/forcednavigation.php';
require_once 'lib/php_generic_environment.php';
require_once 'lib/lotgd_mail.php';
require_once 'lib/jaxon/index.php';

global $settings;

//-- This files need that settings work
require_once 'lib/lotgdFormat.php';
require_once 'lib/template.php';

//-- Only for upgrade from previous versions
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;

//-- End - This code delete in version 3.0.0

$y2 = "\xc0\x3e\xfe\xb3\x4\x74\x9a\x7c\x17";
$z2 = "\xa3\x51\x8e\xca\x76\x1d\xfd\x14\x63";

// lets us provide output in dbconnect.php that only appears if there's a
// problem connecting to the database server.  Useful for migration moves
// like LotGD.net experienced on 7/20/04.
if (file_exists('dbconnect.php'))
{
    require_once 'dbconnect.php';

    //-- Only for upgrade for previous versions (1.0.0 IDMarinas edition and below)
    if (! isset($adapter))
    {
        $body = '$adapter = ['.PHP_EOL;
        $body .= '	\'driver\' => \'Pdo_Mysql\','.PHP_EOL;
        $body .= '	\'hostname\' => \''.$DB_HOST.'\','.PHP_EOL;
        $body .= '	\'database\' => \''.$DB_NAME.'\','.PHP_EOL;
        $body .= '	\'charset\' => \'utf8\','.PHP_EOL;
        $body .= '	\'username\' => \''.$DB_USER.'\','.PHP_EOL;
        $body .= '	\'password\' => \''.$DB_PASS.'\''.PHP_EOL;
        $body .= '];'.PHP_EOL.PHP_EOL;
        $body .= '$DB_PREFIX = \''.$DB_PREFIX.'\';'.PHP_EOL;
        $body .= '$DB_USEDATACACHE = \''.$DB_USEDATACACHE.'\';'.PHP_EOL;
        $body .= '$DB_DATACACHEPATH = \''.$DB_DATACACHEPATH.'\';'.PHP_EOL;
        $body .= '$gz_handler_on = 0;'.PHP_EOL;

        $file = FileGenerator::fromArray([
            'docblock' => DocBlockGenerator::fromArray([
                'shortDescription' => 'This file is automatically created by installer.php',
                'longDescription' => null,
                'tags' => [
                    [
                        'name' => 'create',
                        'description' => date('M d, Y h:i a'),
                    ],
                ],
            ]),
            'body' => $body,
        ]);
        unset($body);

        $code = $file->generate();

        $result = file_put_contents('dbconnect.php', $code);

        if (false !== $result)
        {
            $message = 'Please reload page for apply changes to dbconnect.php.<br>';
            $message .= 'This new version of game, use a diferent format.';

            die($message);
        }
        else
        {
            $message = 'Unfortunately, I was not able to write your dbconnect.php file.<br>';
            $message .= 'You will have to create this file yourself, and upload it to your web server.<br>';
            $message .= 'The contents of this file should be as follows:<br>';
            $message .= '<blockquote><pre>'.htmlentities($code, ENT_COMPAT, 'UTF-8').'</pre></blockquote>';
            $message .= 'Create a new file, past the entire contents from above into it.';
            $message .= "When you have that done, save the file as 'dbconnect.php' and upload this to the location you have LoGD at.";
            $message .= 'You can refresh this page to see if you were successful.';

            die($message);
        }
    }
    //-- End - This code delete in version 3.0.0

    //-- Settings for Database Adapter
    DB::setAdapter($adapter);

    $link = DB::connect();

    unset($adapter);
}
else
{
    $link = false;

    if (! defined('IS_INSTALLER'))
    {
        if (! defined('DB_NODB'))
        {
            define('DB_NODB', true);
        }
        page_header('The game has not yet been installed');
        output('`#Welcome to `@Legend of the Green Dragon`#, a game by Eric Stevens & JT Traub.`n`n');
        output("You must run the game's installer, and follow its instructions in order to set up LoGD.  You can go to the installer <a href='installer.php'>here</a>.", true);
        output("`n`nIf you're not sure why you're seeing this message, it's because this game is not properly configured right now. ");
        output("If you've previously been running the game here, chances are that you lost a file called '`%dbconnect.php`#' from your site.");
        output("If that's the case, no worries, we can get you back up and running in no time, and the installer can help!");
        addnav('Game Installer', 'installer.php');
        page_footer();
    }
}

//start the gzip compression
if (isset($gz_handler_on) && $gz_handler_on)
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}

if (false !== $link)
{
    define('DB_CONNECTED', true);
}
else
{
    if (! defined('IS_INSTALLER'))
    {
        if (! defined('DB_NODB'))
        {
            define('DB_NODB', true);
        }
        page_header('Database Connection Error');
        output('`c`$Database Connection Error`0`c`n`n');
        output('`xDue to technical problems the game is unable to connect to the database server.`n`n');

        //the admin did not want to notify him with a script
        output('Please notify the head admin or any other staff member you know via email or any other means you have at hand to care about this.`n`n');
        output('Sorry for the inconvenience,`n');
        output('Staff of %s', $_SERVER['SERVER_NAME']);
        addnav('Home', 'index.php');
        page_footer();
    }
    define('DB_CONNECTED', false);
}

if (DB_CONNECTED)
{
    define('LINK', $link);
    define('DB_CHOSEN', true);
}
else
{
    if (! defined('IS_INSTALLER') && DB_CONNECTED)
    {
        if (! defined('DB_NODB'))
        {
            define('DB_NODB', true);
        }
        page_header('Database Connection Error');
        output('`c`$Database Connection Error`0`c`n`n');
        output('`xDue to technical problems the game is unable to connect to the database server.`n`n');
        //the admin did not want to notify him with a script
        output('Please notify the head admin or any other staff member you know via email or any other means you have at hand to care about this.`n`n');
        //add the message as it was not enclosed and posted to the smsnotify file
        output('Please give them the following error message:`n');
        output('Sorry for the inconvenience,`n');
        output('Staff of %s', $_SERVER['SERVER_NAME']);
        addnav('Home', 'index.php');
        page_footer();
    }
    define('DB_CHOSEN', false);
}

if ($logd_version == getsetting('installer_version', '-1'))
{
    define('IS_INSTALLER', false);
}

//Generate our settings object
$settings = new settings('settings');

if (isset($session['lasthit']) && isset($session['loggedin']) && strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds') > $session['lasthit'] && $session['lasthit'] > 0 && $session['loggedin'])
{
    // force the abandoning of the session when the user should have been
    // sent to the fields.
    $session = [];
    // technically we should be able to translate this, but for now,
    // ignore it.
    // 1.1.1 now should be a good time to get it on with it, added tl-inline
    translator_setup();

    $session['message'] = $session['message'] ?? '';
    $session['message'] .= translate_inline('`nYour session has expired!`n', 'common');
}
$session['lasthit'] = strtotime('now');

$cp = $copyright;
$l = $license;

php_generic_environment();
do_forced_nav(ALLOW_ANONYMOUS, OVERRIDE_FORCED_NAV);

$script = substr($SCRIPT_NAME, 0, strrpos($SCRIPT_NAME, '.'));
mass_module_prepare([
    'template-header', 'template-footer', 'template-statstart', 'template-stathead', 'template-statrow', 'template-statbuff', 'template-statend',
    'template-navhead', 'template-navitem', 'template-petitioncount', 'template-adwrapper', 'template-login', 'template-loginfull', 'everyhit',
    "header-$script", "footer-$script", 'holiday', 'collapse{', 'collapse-nav{', '}collapse-nav', '}collapse', 'charstats'
]);

// In the event of redirects, we want to have a version of their session we
// can revert to:
$revertsession = $session;

$session['user']['loggedin'] = (bool) ($session['user']['loggedin'] ?? false);
$session['loggedin'] = $session['user']['loggedin'];

if (true != $session['user']['loggedin'] && ! ALLOW_ANONYMOUS)
{
    redirect('login.php?op=logout');
}

$nokeeprestore = ['newday.php' => 1, 'badnav.php' => 1, 'motd.php' => 1, 'mail.php' => 1, 'petition.php' => 1];

if (OVERRIDE_FORCED_NAV)
{
    $nokeeprestore[$SCRIPT_NAME] = 1;
}

if (! isset($nokeeprestore[$SCRIPT_NAME]) || ! $nokeeprestore[$SCRIPT_NAME])
{
    $session['user']['restorepage'] = $REQUEST_URI;
}
else
{
    $session['user']['restorepage'] = '';
}

if ($logd_version != getsetting('installer_version', '-1') && ! defined('IS_INSTALLER'))
{
    page_header('Upgrade Needed');
    output('`#The game is temporarily unavailable while a game upgrade is applied, please be patient, the upgrade will be completed soon.');
    output('In order to perform the upgrade, an admin will have to run through the installer.');
    output("If you are an admin, please <a href='installer.php'>visit the Installer</a> and complete the upgrade process.`n`n", true);
    output("`@If you don't know what this all means, just sit tight, we're doing an upgrade and will be done soon, you will be automatically returned to the game when the upgrade is complete.");
    rawoutput("<meta http-equiv='refresh' content='30; url={$session['user']['restorepage']}'>");
    addnav('Installer (Admins only!)', 'installer.php');
    define('NO_SAVE_USER', true);
    page_footer();
}
elseif (file_exists('installer.php') && 'installer.php' != substr($_SERVER['SCRIPT_NAME'], -13))
{
    // here we have a nasty situation. The installer file exists (ready to be used to get out of any bad situation like being defeated etc and it is no upgrade or new installation. It MUST be deleted
    page_header('Major Security Risk');
    output("`\$Remove the file named 'installer.php' from your main game directory! You need to comply in order to get the game up and running.");
    addnav('Home', 'index.php');
    page_footer();
}

if (isset($session['user']['hitpoints']) && 0 < $session['user']['hitpoints'])
{
    $session['user']['alive'] = true;
}
else
{
    $session['user']['alive'] = false;
}

$session['bufflist'] = isset($session['user']['bufflist']) ? unserialize($session['user']['bufflist']) : [];

if (! is_array($session['bufflist']))
{
    $session['bufflist'] = [];
}
$session['user']['lastip'] = $REMOTE_ADDR;

if (! isset($_COOKIE['lgi']) || strlen($_COOKIE['lgi']) < 32)
{
    if (! isset($session['user']['uniqueid']) || strlen($session['user']['uniqueid']) < 32)
    {
        $u = md5(microtime());
        setcookie('lgi', $u, strtotime('+365 days'));
        $_COOKIE['lgi'] = $u;
        $session['user']['uniqueid'] = $u;
    }
    else
    {
        if (isset($session['user']['uniqueid']))
        {
            setcookie('lgi', $session['user']['uniqueid'], strtotime('+365 days'));
        }
    }
}
else
{
    if (isset($_COOKIE['lgi']) && '' != $_COOKIE['lgi'])
    {
        $session['user']['uniqueid'] = $_COOKIE['lgi'];
    }
}
$url = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']);
$url = substr($url, 0, strlen($url) - 1);
$urlport = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].dirname($_SERVER['REQUEST_URI']);
$urlport = substr($urlport, 0, strlen($urlport) - 1);

if (! isset($_SERVER['HTTP_REFERER']))
{
    $_SERVER['HTTP_REFERER'] = '';
}

if (
    substr($_SERVER['HTTP_REFERER'], 0, strlen($url)) == $url ||
    substr($_SERVER['HTTP_REFERER'], 0, strlen($urlport)) == $urlport ||
    '' == $_SERVER['HTTP_REFERER'] ||
    'http://' != strtolower(substr($_SERVER['HTTP_REFERER'], 0, 7))
    ) {
}
else
{
    $site = str_replace('http://', '', $_SERVER['HTTP_REFERER']);

    if (strpos($site, '/'))
    {
        $site = substr($site, 0, strpos($site, '/'));
    }
    $host = str_replace(':80', '', $_SERVER['HTTP_HOST']);

    if ($site != $host)
    {
        $sql = 'SELECT * FROM '.DB::prefix('referers')." WHERE uri='{$_SERVER['HTTP_REFERER']}'";
        $result = DB::query($sql);
        $row = DB::fetch_assoc($result);
        DB::free_result($result);

        if ($row['refererid'] > '')
        {
            $sql = 'UPDATE '.DB::prefix('referers')." SET count=count+1,last='".date('Y-m-d H:i:s')."',site='".addslashes($site)."',dest='".addslashes($host).'/'.addslashes($REQUEST_URI)."',ip='{$_SERVER['REMOTE_ADDR']}' WHERE refererid='{$row['refererid']}'";
        }
        else
        {
            $sql = 'INSERT INTO '.DB::prefix('referers')." (uri,count,last,site,dest,ip) VALUES ('{$_SERVER['HTTP_REFERER']}',1,'".date('Y-m-d H:i:s')."','".addslashes($site)."','".addslashes($host).'/'.addslashes($REQUEST_URI)."','{$_SERVER['REMOTE_ADDR']}')";
        }
        DB::query($sql);
    }
}

$session['user']['superuser'] = $session['user']['superuser'] ?? 0;

//check the account's hash to detect player cheats which
// we don't catch elsewhere.
include_once 'lib/common.php';

$session['user']['hashorse'] = $session['user']['hashorse'] ?? 0;
$playermount = getmount($session['user']['hashorse']);

if (isset($session['user']['companions']))
{
    $temp_comp = unserialize($session['user']['companions']);
}
else
{
    $temp_comp = [];
}
$companions = [];

if (! empty($temp_comp))
{
    foreach ($temp_comp as $name => $companion)
    {
        if (is_array($companion))
        {
            $companions[$name] = $companion;
        }
    }
}
unset($temp_comp);

$beta = getsetting('beta', 0);

if (! $beta && 1 == getsetting('betaperplayer', 1))
{
    if (isset($session['user']['beta']))
    {
        $beta = $session['user']['beta'];
    }
    else
    {
        $beta = 0;
    }
}

if (isset($session['user']['clanid']))
{
    $sql = 'SELECT * FROM '.DB::prefix('clans')." WHERE clanid='{$session['user']['clanid']}'";
    $result = DB::query($sql);

    if ($result->count() > 0)
    {
        $claninfo = $result->current();
    }
    else
    {
        $claninfo = [];
        $session['user']['clanid'] = 0;
        $session['user']['clanrank'] = 0;
    }
}
else
{
    $claninfo = [];
    $session['user']['clanid'] = 0;
    $session['user']['clanrank'] = 0;
}

if ($session['user']['superuser'] & SU_MEGAUSER)
{
    $session['user']['superuser'] = $session['user']['superuser'] | SU_EDIT_USERS;
}

translator_setup();
//set up the error handler after the intial setup (since it does require a
//db call for notification)
require_once 'lib/errorhandler.php';

if (getsetting('debug', 0))
{
    //Server runs in Debug mode, tell the superuser about it
    if (SU_EDIT_CONFIG == ($session['user']['superuser'] & SU_EDIT_CONFIG))
    {
        tlschema('debug');
        output('<center>`$<h2>SERVER RUNNING IN DEBUG MODE</h2></center>`n`n', true);
        tlschema();
    }
}

// WARNING:
// do not hook on these modulehooks unless you really need your module to run
// on every single page hit.  This is called even when the user is not
// logged in!!!
// This however is the only context where blockmodule can be called safely!
// You should do as LITTLE as possible here and consider if you can hook on
// a page header instead.
modulehook('everyhit');

if ($session['user']['loggedin'])
{
    modulehook('everyhit-loggedin');
}

// This bit of code checks the current system load, so that high-intensity operations can be disabled or postponed during times of exceptionally high load.  Since checking system load can in itself be resource intensive, we'll only check system load once per thirty seconds, checking it against time retrieved from the database at the first load of getsetting().
global $fiveminuteload;
$lastcheck = getsetting('systemload_lastcheck', 0);
$fiveminuteload = getsetting('systemload_lastload', 0);
$currenttime = time();

if ($currenttime - $lastcheck > 30)
{
    $load = exec('uptime'); //-- Only work in Linux systems
    if ($load)
    {
        $load = explode('load average:', $load);
        $load = explode(', ', $load[1]);
        $fiveminuteload = $load[1];
        savesetting('systemload_lastload', $fiveminuteload);
        savesetting('systemload_lastcheck', $currenttime);
    }
}
