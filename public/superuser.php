<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Event\Superuser;

require_once 'common.php';

check_su_access(0xFFFFFFFF & ~SU_DOESNT_GIVE_GROTTO);

$textDomain = 'grotto_superuser';

$op = LotgdRequest::getQuery('op');

if ('keepalive' == $op)
{
    $repository = Doctrine::getRepository('LotgdCore:User');
    $entity     = $repository->find($session['user']['acctid']);

    $entity->setLaston(new DateTime('now'));

    Doctrine::persist($entity);
    Doctrine::flush();

    echo '<html><meta http-equiv="Refresh" content="30;url='.LotgdRequest::getServer('REQUEST_URI').'"></html><body>'.date('Y-m-d H:i:s').'</body></html>';

    exit();
}

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

LotgdNavigation::superuserGrottoNav();
LotgdNavigation::addNav('superuser.nav.logout', 'login.php?op=logout');

LotgdNavigation::addHeader('superuser.category.actions');

($session['user']['superuser'] & SU_EDIT_PETITIONS) && LotgdNavigation::addNav('superuser.nav.petition', 'viewpetition.php');

if (($session['user']['superuser'] & SU_EDIT_COMMENTS) !== 0)
{
    LotgdNavigation::addNav('superuser.nav.moderation', 'moderate.php');
    LotgdNavigation::addNav('superuser.nav.bios', 'bios.php');
}

($session['user']['superuser'] & SU_EDIT_DONATIONS) && LotgdNavigation::addNav('superuser.nav.donation', 'donators.php');

if (file_exists('public/paylog.php') && $session['user']['superuser'] & SU_EDIT_PAYLOG)
{
    LotgdNavigation::addNav('superuser.nav.paylog', 'paylog.php');
}

($session['user']['superuser'] & SU_RAW_SQL) && LotgdNavigation::addNav('superuser.nav.rawsql', 'rawsql.php');

($session['user']['superuser'] & SU_IS_TRANSLATOR) && LotgdNavigation::addNav('superuser.nav.untranslated', 'untranslated.php');

LotgdNavigation::addHeader('superuser.category.editors');

if (($session['user']['superuser'] & SU_EDIT_USERS) !== 0)
{
    LotgdNavigation::addNav('superuser.nav.user', 'user.php');
    LotgdNavigation::addNav('superuser.nav.titleedit', 'titleedit.php');
}

($session['user']['superuser'] & SU_EDIT_BANS) && LotgdNavigation::addNav('superuser.nav.bans', 'bans.php');

if (($session['user']['superuser'] & SU_EDIT_MOUNTS) !== 0)
{
    LotgdNavigation::addNav('superuser.nav.mounts', 'mounts.php');
    LotgdNavigation::addNav('superuser.nav.companions', 'companions.php');
}

if (($session['user']['superuser'] & SU_EDIT_CREATURES) !== 0)
{
    LotgdNavigation::addNav('superuser.nav.creatures', 'creatures.php');
    LotgdNavigation::addNav('superuser.nav.masters', 'masters.php');
}

if (($session['user']['superuser'] & SU_EDIT_EQUIPMENT) !== 0)
{
    LotgdNavigation::addNav('superuser.nav.weaponeditor', 'weaponeditor.php');
    LotgdNavigation::addNav('superuser.nav.armoreditor', 'armoreditor.php');
}

LotgdNavigation::addHeader('superuser.category.mechanics');

($session['user']['superuser'] & SU_EDIT_CONFIG) && LotgdNavigation::addNav('superuser.nav.configuration', 'configuration.php');
($session['user']['superuser'] & SU_EDIT_USERS)  && LotgdNavigation::addNav('superuser.nav.backup', 'characterbackup.php');

if (($session['user']['superuser'] & SU_EDIT_CONFIG) !== 0)
{
    LotgdNavigation::addNav('superuser.nav.debug', 'debug.php');
    LotgdNavigation::addNav('superuser.nav.referers', 'referers.php');
    LotgdNavigation::addNav('superuser.nav.stats', 'stats.php');
    LotgdNavigation::addNav('superuser.nav.gamelog', 'gamelog.php');
}

($session['user']['superuser'] & SU_MEGAUSER) && LotgdNavigation::addNav('superuser.nav.globaluserfunctions', 'globaluserfunctions.php');

LotgdNavigation::addHeader('superuser.category.module');

$args = new Superuser([]);
LotgdEventDispatcher::dispatch($args, Superuser::SUPERUSER);

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/superuser.html.twig', [
    'textDomain' => $textDomain,
]));

//-- Finalize page
LotgdResponse::pageEnd();
