<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/commentary.php';
require_once 'lib/sanitize.php';

check_su_access(0xFFFFFFFF & ~SU_DOESNT_GIVE_GROTTO);

tlschema('superuser');


$textDomain = 'page-superuser';

$op = \LotgdHttp::getQuery('op');

if ('keepalive' == $op)
{
    $sql = 'UPDATE '.DB::prefix('accounts')." SET laston='".date('Y-m-d H:i:s')."' WHERE acctid='{$session['user']['acctid']}'";
    DB::query($sql);

    echo '<html><meta http-equiv="Refresh" content="30;url='.LotgdHttp::getServer('REQUEST_URI').'"></html><body>'.date('Y-m-d H:i:s').'</body></html>';

    exit();
}

page_header('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();
\LotgdNavigation::addNav('superuser.nav.logout', 'login.php?op=logout');

\LotgdNavigation::addHeader('superuser.category.actions');

($session['user']['superuser'] & SU_EDIT_PETITIONS) and \LotgdNavigation::addNav('superuser.nav.petition', 'viewpetition.php');

if ($session['user']['superuser'] & SU_EDIT_COMMENTS)
{
    \LotgdNavigation::addNav('superuser.nav.moderation', 'moderate.php');
    \LotgdNavigation::addNav('superuser.nav.bios', 'bios.php');
}

($session['user']['superuser'] & SU_EDIT_DONATIONS) and \LotgdNavigation::addNav('superuser.nav.donation', 'donators.php');

(file_exists('paylog.php') && $session['user']['superuser'] & SU_EDIT_PAYLOG) and \LotgdNavigation::addNav('superuser.nav.paylog', 'paylog.php');

($session['user']['superuser'] & SU_RAW_SQL) and \LotgdNavigation::addNav('superuser.nav.rawsql', 'rawsql.php');

($session['user']['superuser'] & SU_IS_TRANSLATOR) and \LotgdNavigation::addNav('superuser.nav.untranslated', 'untranslated.php');


\LotgdNavigation::addHeader('superuser.category.editors');

if ($session['user']['superuser'] & SU_EDIT_USERS)
{
    \LotgdNavigation::addNav('superuser.nav.user', 'user.php');
    \LotgdNavigation::addNav('superuser.nav.titleedit', 'titleedit.php');
}

($session['user']['superuser'] & SU_EDIT_BANS) and \LotgdNavigation::addNav('superuser.nav.bans', 'bans.php');

if ($session['user']['superuser'] & SU_EDIT_MOUNTS)
{
    \LotgdNavigation::addNav('superuser.nav.mounts', 'mounts.php');
    \LotgdNavigation::addNav('superuser.nav.companions', 'companions.php');
}

if ($session['user']['superuser'] & SU_EDIT_CREATURES)
{
    \LotgdNavigation::addNav('superuser.nav.creatures', 'creatures.php');
    \LotgdNavigation::addNav('superuser.nav.taunt', 'taunt.php');
    \LotgdNavigation::addNav('superuser.nav.deathmessages', 'deathmessages.php');
    \LotgdNavigation::addNav('superuser.nav.masters', 'masters.php');
}

if ($session['user']['superuser'] & SU_EDIT_EQUIPMENT)
{
    \LotgdNavigation::addNav('superuser.nav.weaponeditor', 'weaponeditor.php');
    \LotgdNavigation::addNav('superuser.nav.armoreditor', 'armoreditor.php');
}

($session['user']['superuser'] & SU_EDIT_COMMENTS) and \LotgdNavigation::addNav('superuser.nav.badword', 'badword.php');

($session['user']['superuser'] & SU_MANAGE_MODULES) and \LotgdNavigation::addNav('superuser.nav.modules', 'modules.php');


\LotgdNavigation::addHeader('superuser.category.mechanics');

($session['user']['superuser'] & SU_MEGAUSER) and \LotgdNavigation::addNav('superuser.nav.globaluserfunctions', 'globaluserfunctions.php');

if ($session['user']['superuser'] & SU_EDIT_CONFIG)
{
    \LotgdNavigation::addNav('superuser.nav.configuration', 'configuration.php');
    \LotgdNavigation::addNav('superuser.nav.debug', 'debug.php');
    \LotgdNavigation::addNav('superuser.nav.stats', 'stats.php');
    file_exists('gamelog.php') and \LotgdNavigation::addNav('superuser.nav.gamelog', 'gamelog.php');
}

\LotgdNavigation::addHeader('superuser.category.module');

modulehook('superuser', [], true);

//-- This is only for params not use for other purpose
$params = modulehook('page-superuser-tpl-params', []);
rawoutput(\LotgdTheme::renderThemeTemplate('page/superuser.twig', $params));

page_footer();
