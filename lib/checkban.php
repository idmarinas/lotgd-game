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
    elseif (false === $login)
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

    $query = $repository->createQueryBuilder('u');
    $result = $query->where("((substring(:ip ,1 , length(u.ipfilter)) = u.ipfilter AND u.ipfilter != '') OR (u.uniqueid = :id AND u.uniqueid != '')) AND (u.banexpire = '0000-00-00' OR u.banexpire >= :date)")

        ->setParameter('ip', $ip)
        ->setParameter('id', $id)
        ->setParameter('date', new \DateTime('now'))

        ->getQuery()
        ->getResult()
    ;

    if (count($result))
    {
        $session = [];
        $session['message'] .= \LotgdTranslator::t('checkban.ban', [], 'page-bans');

        foreach ($result as $row)
        {
            $session['message'] .= $row->getBanreason().'`n';

            $message = \LotgdTranslator::t('checkban.expire.time', ['date' => $row->getBanexpire()], 'page-bans');
            if (new \DateTime('0000-00-00') == $row->getBanexpire() || new \DateTime('0000-00-00 00:00:00') == $row->getBanexpire())
            {
                $message = \LotgdTranslator::t('checkban.expire.permanent', [], 'page-bans');
            }

            $session['message'] .= $message;

            $row->setLasthit(new \DateTime('now'));
            \Doctrine::persist($row);
            \Doctrine::flush();

            $session['message'] .= '`n';
            $session['message'] .= \LotgdTranslator::t('checkban.by', ['by' => $row['banner']], 'page-bans');
        }
        $session['message'] .= \LotgdTranslator::t('checkban.note', [], 'page-bans');
        header('Location: index.php');

        exit();
    }
}
