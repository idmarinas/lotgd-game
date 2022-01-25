<?php

use Jaxon\Response\Response;
use Lotgd\Core\Event\EveryRequest;
use Lotgd\Core\Kernel;

/*
 * The intention of duplicating the "common.php" file is to remove everything
 * that does not make sense in a Jaxon-PHP request.
 * This will speed up the processing of a Jaxon-PHP request a bit.
 */

// **** NOTICE *****
// This series of scripts (collectively known as Legend of the Green Dragon
// or LotGD) is licensed according to the Creating Commons Attribution
// Non-commercial Share-alike license.  The terms of this license must be
// followed for you to legally use or distribute this software.   This
// license must be used on the distribution of any works derived from this
// work.
// Please see the file LICENSE for a full textual description of the license.txt.
require_once './common_common.php';

if (
    isset($session['lasthit'], $session['loggedin'])

    && strtotime('-'.LotgdSetting::getSetting('LOGINTIMEOUT', 900).' seconds') > $session['lasthit']
    && $session['lasthit'] > 0 && $session['loggedin']
) {
    // force the abandoning of the session when the user should have been
    // sent to the fields.
    LotgdSession::invalidate();

    $session = [];

    $response = new Response();

    $response->dialog->warning(LotgdTranslator::t('session.timeout', [], 'app_default'));

    return $response;
}
$session['lasthit'] = strtotime('now');

$cp = Kernel::COPYRIGHT;
$l  = Kernel::LICENSE;

do_forced_nav(ALLOW_ANONYMOUS, OVERRIDE_FORCED_NAV);

//-- Check if have a full maintenance mode activate force to log out all players
if (LotgdSetting::getSetting('fullmaintenance', 0) && (($session['user']['loggedin'] ?? false) && 0 >= ($session['user']['superuser'] & SU_DEVELOPER)))
{
    $session['user']['restorepage'] = 'news.php';

    if ($session['user']['location'] == $iname)
    {
        $session['user']['restorepage'] = 'inn.php?op=strolldown';
    }

    LotgdKernel::get('cache.app')->delete('char-list-home-page');

    LotgdTool::saveUser();

    LotgdSession::invalidate();

    $session = [];
}

$script = substr(LotgdRequest::getServer('SCRIPT_NAME'), 0, strrpos(LotgdRequest::getServer('SCRIPT_NAME'), '.'));
mass_module_prepare(['everyhit', "header-{$script}", "footer-{$script}", 'holiday', 'charstats']);

// In the event of redirects, we want to have a version of their session we
// can revert to:
$revertsession = $session;

$session['user']['loggedin'] = (bool) ($session['user']['loggedin'] ?? false);
$session['loggedin']         = $session['user']['loggedin'];

if ( ! $session['user']['loggedin'] && ! ALLOW_ANONYMOUS)
{
    exit;
}

$nokeeprestore = ['newday.php' => 1, 'badnav.php' => 1, 'motd.php' => 1, 'mail.php' => 1, 'petition.php' => 1];

if (OVERRIDE_FORCED_NAV)
{
    $nokeeprestore[LotgdRequest::getServer('SCRIPT_NAME')] = 1;
}

if ( ! isset($nokeeprestore[LotgdRequest::getServer('SCRIPT_NAME')]) || ! $nokeeprestore[LotgdRequest::getServer('SCRIPT_NAME')])
{
    $session['user']['restorepage'] = LotgdRequest::getServer('REQUEST_URI');
}

$session['user']['alive'] = false;

if (isset($session['user']['hitpoints']) && 0 < $session['user']['hitpoints'])
{
    $session['user']['alive'] = true;
}

$session['bufflist'] = array_map('array_filter', $session['user']['bufflist'] ?? []);

if ( ! \is_array($session['bufflist']))
{
    $session['bufflist'] = [];
}
$session['user']['lastip']    = LotgdRequest::getServer('REMOTE_ADDR');
$session['user']['superuser'] = $session['user']['superuser'] ?? 0;

//check the account's hash to detect player cheats which
// we don't catch elsewhere.
include_once 'lib/common.php';

$session['user']['hashorse'] = $session['user']['hashorse'] ?? 0;
$playermount                 = LotgdTool::getMount($session['user']['hashorse']);

$temp_comp  = $session['user']['companions'] ?? [];
$companions = [];

if ( ! empty($temp_comp))
{
    $companions = array_filter($temp_comp, 'is_array');
}
unset($temp_comp);

$claninfo = [];

if ($session['user']['clanid'] ?? false)
{
    $repository = Doctrine::getRepository('LotgdCore:Clans');
    $entity     = $repository->find($session['user']['clanid']);

    if ($entity)
    {
        $claninfo = $repository->extractEntity($entity);
    }
    else
    {
        $session['user']['clanid']   = 0;
        $session['user']['clanrank'] = 0;
    }
}
else
{
    $session['user']['clanid']   = 0;
    $session['user']['clanrank'] = 0;
}

if (($session['user']['superuser'] & SU_MEGAUSER) !== 0)
{
    $session['user']['superuser'] |= SU_EDIT_USERS;
}

// WARNING:
// do not hook on these modulehooks unless you really need your module to run
// on every single page hit.  This is called even when the user is not
// logged in!!!
// This however is the only context where blockmodule can be called safely!
// You should do as LITTLE as possible here and consider if you can hook on
// a page header instead.
LotgdEventDispatcher::dispatch(new EveryRequest(), EveryRequest::HIT);
modulehook('everyhit');

if ($session['user']['loggedin'])
{
    LotgdEventDispatcher::dispatch(new EveryRequest(), EveryRequest::HIT_AUTHENTICATED);
    modulehook('everyhit-loggedin');
}
