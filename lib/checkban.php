<?php

// translator ready
// addnews ready
// mail ready
function checkban($login = false)
{
    global $session;

    if ($session['banoverride'] ?? false)
    {
        return false;
    }

    if (false === $login)
    {
        $request = \LotgdLocator::get(\Lotgd\Core\Http::class);
        $cookie = $request->getCookie();
        $ip = $request->getServer('REMOTE_ADDR');
        $id = $cookie->offsetExists('lgi') ? $cookie->offsetGet('lgi') : '';
    }
    else
    {
        $repository = \Doctrine::getRepository('LotgdCore:Accounts');
        $result = $repository->findBy([ 'login' => $login ]);

        if ($result['banoverride'] || ($result['superuser'] & ~SU_DOESNT_GIVE_GROTTO))
        {
            $session['banoverride'] = true;

            return false;
        }

        $ip = $result['lastip'];
        $id = $result['uniqueid'];
    }

    $repository = \Doctrine::getRepository('LotgdCore:Bans');
    $repository->removeExpireBans();

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
                $session['message'] .= sprintf_translate('  `^This ban will be removed `$after`^ %s.`0', date('H:i, M d, Y', strtotime($row['banexpire'])));
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
