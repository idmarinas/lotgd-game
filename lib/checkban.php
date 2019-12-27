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
        $result = $repository->extractEntity($repository->findOneBy([ 'login' => $login ]));

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
        $session['message'] .= \LotgdTranslator::t('checkban.ban', [], 'page-bans');

        while ($row = DB::fetch_assoc($result))
        {
            $session['message'] .= $row['banreason'].'`n';

            if (new \DateTime('0000-00-00') == $row['banexpire'] || new \DateTime('0000-00-00 00:00:00') == $row['banexpire'])
            {
                $session['message'] .= \LotgdTranslator::t('checkban.expire.permanent', [], 'page-bans');
            }
            else
            {
                $session['message'] .= \LotgdTranslator::t('checkban.expire.time', ['date' => $row['banexpire']], 'page-bans');
            }
            $sql = 'UPDATE '.DB::prefix('bans')." SET lasthit='".date('Y-m-d H:i:s')."' WHERE ipfilter='{$row['ipfilter']}' AND uniqueid='{$row['uniqueidid']}'";
            DB::query($sql);
            $session['message'] .= '`n';
            $session['message'] .= \LotgdTranslator::t('checkban.by', ['by' => $row['banner']], 'page-bans');
        }
        $session['message'] .= \LotgdTranslator::t('checkban.note', [], 'page-bans');
        header('Location: index.php');

        exit();
    }
    DB::free_result($result);
}
