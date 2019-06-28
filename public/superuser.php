<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

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

($session['user']['superuser'] & SU_EDIT_PETITIONS) && \LotgdNavigation::addNav('superuser.nav.petition', 'viewpetition.php');

if ($session['user']['superuser'] & SU_EDIT_COMMENTS)
{
    \LotgdNavigation::addNav('superuser.nav.moderation', 'moderate.php');
    \LotgdNavigation::addNav('superuser.nav.bios', 'bios.php');
}

($session['user']['superuser'] & SU_EDIT_DONATIONS) && \LotgdNavigation::addNav('superuser.nav.donation', 'donators.php');

(file_exists('public/paylog.php') && $session['user']['superuser'] & SU_EDIT_PAYLOG) && \LotgdNavigation::addNav('superuser.nav.paylog', 'paylog.php');

($session['user']['superuser'] & SU_RAW_SQL) && \LotgdNavigation::addNav('superuser.nav.rawsql', 'rawsql.php');

($session['user']['superuser'] & SU_IS_TRANSLATOR) && \LotgdNavigation::addNav('superuser.nav.untranslated', 'untranslated.php');

\LotgdNavigation::addHeader('superuser.category.editors');

if ($session['user']['superuser'] & SU_EDIT_USERS)
{
    \LotgdNavigation::addNav('superuser.nav.user', 'user.php');
    \LotgdNavigation::addNav('superuser.nav.titleedit', 'titleedit.php');
}

($session['user']['superuser'] & SU_EDIT_BANS) && \LotgdNavigation::addNav('superuser.nav.bans', 'bans.php');

if ($session['user']['superuser'] & SU_EDIT_MOUNTS)
{
    \LotgdNavigation::addNav('superuser.nav.mounts', 'mounts.php');
    \LotgdNavigation::addNav('superuser.nav.companions', 'companions.php');
}

if ($session['user']['superuser'] & SU_EDIT_CREATURES)
{
    \LotgdNavigation::addNav('superuser.nav.creatures', 'creatures.php');
    \LotgdNavigation::addNav('superuser.nav.masters', 'masters.php');
}

if ($session['user']['superuser'] & SU_EDIT_EQUIPMENT)
{
    \LotgdNavigation::addNav('superuser.nav.weaponeditor', 'weaponeditor.php');
    \LotgdNavigation::addNav('superuser.nav.armoreditor', 'armoreditor.php');
}

($session['user']['superuser'] & SU_MANAGE_MODULES) && \LotgdNavigation::addNav('superuser.nav.modules', 'modules.php');

\LotgdNavigation::addHeader('superuser.category.mechanics');

($session['user']['superuser'] & SU_MEGAUSER) && \LotgdNavigation::addNav('superuser.nav.globaluserfunctions', 'globaluserfunctions.php');

if ($session['user']['superuser'] & SU_EDIT_CONFIG)
{
    \LotgdNavigation::addNav('superuser.nav.configuration', 'configuration.php');
    \LotgdNavigation::addNav('superuser.nav.debug', 'debug.php');
    \LotgdNavigation::addNav('superuser.nav.referers', 'referers.php');
    \LotgdNavigation::addNav('superuser.nav.stats', 'stats.php');
    file_exists('public/gamelog.php') && \LotgdNavigation::addNav('superuser.nav.gamelog', 'gamelog.php');
}

\LotgdNavigation::addHeader('superuser.category.module');

modulehook('superuser', [], true);

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/superuser.twig', []));
page_footer();
