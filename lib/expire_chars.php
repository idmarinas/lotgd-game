<?php

// translator ready
// addnews ready
// mail ready

require_once 'lib/constants.php';
require_once 'lib/settings_extended.php';

$lastexpire = strtotime(getsetting('last_char_expire', '0000-00-00 00:00:00'));
$needtoexpire = strtotime('-23 hours');

if ($lastexpire < $needtoexpire)
{
    savesetting('last_char_expire', date('Y-m-d H:i:s'));
    $old = getsetting('expireoldacct', 45);
    $new = getsetting('expirenewacct', 10);
    $trash = getsetting('expiretrashacct', 1);

    // First, get the account ids to delete the user prefs.
    $sql1 = 'SELECT login,acctid,dragonkills,level FROM '.DB::prefix('accounts').' WHERE (superuser&'.NO_ACCOUNT_EXPIRATION.")=0 AND (1=0\n".($old > 0 ? 'OR (laston < "'.date('Y-m-d H:i:s', strtotime("-$old days"))."\")\n" : '').($new > 0 ? 'OR (laston < "'.date('Y-m-d H:i:s', strtotime("-$new days"))."\" AND level=1 AND dragonkills=0)\n" : '').($trash > 0 ? 'OR (regdate < date_add(NOW(),interval -'.$trash." day) AND laston < regdate)\n" : '').')';
    $result1 = DB::query($sql1);
    $acctids = [];
    $pinfo = [];
    $dk0lvl = 0;
    $dk0ct = 0;
    $dk1lvl = 0;
    $dk1ct = 0;
    $dks = 0;

    while ($row1 = DB::fetch_assoc($result1))
    {
        require_once 'lib/charcleanup.php';

        if (! char_cleanup($row1['acctid'], CHAR_DELETE_AUTO))
        {
            continue;
        }
        array_push($acctids, $row1['acctid']);
        array_push($pinfo, "{$row1['login']}:dk{$row1['dragonkills']}-lv{$row1['level']}");

        if (0 == $row1['dragonkills'])
        {
            $dk0lvl += $row1['level'];
            $dk0ct++;
        }
        elseif (1 == $row1['dragonkills'])
        {
            $dk1lvl += $row1['level'];
            $dk1ct++;
        }
        $dks += $row1['dragonkills'];
    }

    //Log which accounts were deleted.
    $msg = "[{$dk0ct}] with 0 dk avg lvl [".round($dk0lvl / max(1, $dk0ct), 2)."]\n";
    $msg .= "[{$dk1ct}] with 1 dk avg lvl [".round($dk1lvl / max(1, $dk1ct), 2)."]\n";
    $msg .= 'Avg DK: ['.round($dks / max(1, count($acctids)), 2)."]\n";
    $msg .= 'Accounts: '.join($pinfo, ', ');
    require_once 'lib/gamelog.php';
    gamelog('Deleted '.count($acctids)." accounts:\n$msg", 'char expiration');

    // Now delete the accounts themselves
    // one less search pass, and a guarantee that the same accounts selected
    // above are the ones deleted here.
    if (count($acctids))
    {
        $sql = 'DELETE FROM '.DB::prefix('accounts').
            ' WHERE acctid IN ('.join($acctids, ',').')';
        DB::query($sql);
    }

    //adjust for notification - don't notify total newbie chars
    $old = max(1, $old - getsetting('notifydaysbeforedeletion', 5)); //a minimum of 1 day is necessary
    $sql = 'SELECT login,acctid,emailaddress FROM '.DB::prefix('accounts').' WHERE 1=0 '.($old > 0 ? 'OR (laston < "'.date('Y-m-d H:i:s', strtotime("-$old days"))."\")\n" : '')." AND emailaddress!='' AND sentnotice=0 AND (superuser&".NO_ACCOUNT_EXPIRATION.')=0';
    $result = DB::query($sql);
    //we could translate this for each user in its language - but wayy to much ressources. Use your default language instead.
    $subject = translate_inline($settings_extended->getSetting('expirationnoticesubject'));
    $message = translate_inline($settings_extended->getSetting('expirationnoticetext'));

    $message = str_replace('{server}', getsetting('serverurl', 'http://nodomain.notd'), $message);
    /*
    if you run this via cron, you will get nothing. We will use the setting for lotgdnet, even if not used.
    $_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']==80?"":":".$_SERVER['SERVER_PORT']).$_SERVER['SCRIPT_NAME']." is about to expire.  If you wish to keep this character, you should log on to him or her soon!",
    */

    $mheader = 'MIME-Version: 1.0'."\r\n";
    $mheader .= 'Content-type: text/plain; charset='.getsetting('charset', 'UTF-8')."\r\n";
    $mheader .= 'From: '.getsetting('gameadminemail', 'postmaster@localhost')."\r\n";
    $collector = [];

    while ($row = DB::fetch_assoc($result))
    {
        lotgd_mail($row['emailaddress'], $subject, str_replace('{charname}', $row['login'], $message), $mheader);
        $collector[] = $row['acctid'];
    }

    if (! empty($collector))
    {
        $sql = 'UPDATE '.DB::prefix('accounts').' SET sentnotice=1 WHERE acctid IN ('.implode(',', $collector).');';
        DB::query($sql);
    }
}
