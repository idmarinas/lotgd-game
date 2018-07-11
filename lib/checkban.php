<?php

// translator ready
// addnews ready
// mail ready
function checkban($login = false)
{
    global $session;

    if (isset($session['banoverride']) && $session['banoverride'])
    {
        return false;
    }

    if (false === $login)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $id = isset($_COOKIE['lgi']) ? $_COOKIE['lgi'] : '';
    }
    else
    {
        $sql = 'SELECT lastip,uniqueid,banoverride,superuser FROM '.DB::prefix('accounts')." WHERE login='$login'";
        $result = DB::query($sql);
        $row = DB::fetch_assoc($result);

        if ($row['banoverride'] || ($row['superuser'] & ~SU_DOESNT_GIVE_GROTTO))
        {
            $session['banoverride'] = true;

            return false;
        }
        DB::free_result($result);
        $ip = $row['lastip'];
        $id = $row['uniqueid'];
    }
    //first, remove bans, then select them.
    DB::query('DELETE FROM '.DB::prefix('bans')." WHERE banexpire < NOW() AND banexpire>'0000-00-00 00:00:00'");

    $sql = 'SELECT * FROM '.DB::prefix('bans')." where ((substring('$ip',1,length(ipfilter))=ipfilter AND ipfilter<>'') OR (uniqueid='$id' AND uniqueid<>'')) AND (banexpire='0000-00-00' OR banexpire>=NOW())";
    $result = DB::query($sql);

    if (DB::num_rows($result) > 0)
    {
        $session = [];
        tlschema('ban');
        $session['message'] .= translate_inline('`n`4You fall under a ban currently in place on this website:`n');

        while ($row = DB::fetch_assoc($result))
        {
            $session['message'] .= $row['banreason'].'`n';

            if ('0000-00-00' == $row['banexpire'] || '0000-00-00 00:00:00' == $row['banexpire'])
            {
                $session['message'] .= translate_inline('  `$This ban is permanent!`0');
            }
            else
            {
                $session['message'] .= sprintf_translate('  `^This ban will be removed `$after`^ %s.`0', date('M d, Y', strtotime($row['banexpire'])));
            }
            $sql = 'UPDATE '.DB::prefix('bans')." SET lasthit='".date('Y-m-d H:i:s')."' WHERE ipfilter='{$row['ipfilter']}' AND uniqueid='{$row['uniqueidid']}'";
            DB::query($sql);
            $session['message'] .= '`n';
            $session['message'] .= sprintf_translate('`n`4The ban was issued by %s`^.`n', $row['banner']);
        }
        $session['message'] .= translate_inline('`4If you wish, you may appeal your ban with the petition link.');
        tlschema();
        header('Location: index.php');

        exit();
    }
    DB::free_result($result);
}
