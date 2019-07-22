<?php

// translator ready
// addnews ready
// mail ready

require_once 'lib/constants.php';
require_once 'lib/charcleanup.php';
require_once 'lib/gamelog.php';

$lastexpire = strtotime(getsetting('last_char_expire', '0000-00-00 00:00:00'));
$needtoexpire = strtotime('-23 hours');

if ($lastexpire >= $needtoexpire)
{
    return;
}

savesetting('last_char_expire', date('Y-m-d H:i:s'));

$old = (int) getsetting('expireoldacct', 45);
$new = (int) getsetting('expirenewacct', 10);
$trash = (int) getsetting('expiretrashacct', 1);

$repository = \Doctrine::getRepository('LotgdCore:Characters');
$query = $repository->createQueryBuilder('u');
$expr = $query->expr();

$dateOld = new \DateTime('now');
$dateOld->sub(new \DateInterval("P{$old}D"));

$dateNew = new \DateTime('now');
$dateNew->sub(new \DateInterval("P{$new}D"));

$dateTrash = new \DateTime('now');
$dateTrash->sub(new \DateInterval("P{$trash}D"));

$query
    ->where('BIT_AND(a.superuser, :permit) = 0')
    ->andWhere($expr->orX(
        '1 = 0',
        $old ? $expr->lt('a.laston', ':dateOld') : null,
        $new ? $expr->andX($expr->lt('a.laston', ':dateNew'), $expr->eq('u.level', 1), $expr->eq('u.dragonkills', 0)) : null,
        $trash ? $expr->andX($expr->lt('a.regdate', ':dateTrash'), $expr->eq('a.laston', 'a.regdate')) : null
    ))
    ->leftJoin('LotgdCore:Accounts', 'a', 'with', $expr->eq('a.acctid', 'u.acct'))

    ->setParameter('permit', NO_ACCOUNT_EXPIRATION)
;

($old) ? $query->setParameter('dateOld', $dateOld) : null;
($new) ? $query->setParameter('dateNew', $dateNew) : null;
($trash) ? $query->setParameter('dateTrash', $dateTrash) : null;

$pinfo = [];
$dk0lvl = 0;
$dk0ct = 0;
$dk1lvl = 0;
$dk1ct = 0;
$dks = 0;

$result = $query->getQuery()->getResult();

foreach ($result as $entity)
{
    //-- Delete account and data related
    if (! char_cleanup($entity->getAcct()->getAcctid(), CHAR_DELETE_AUTO))
    {
        continue;
    }

    array_push($pinfo, "{$entity->getAcct()->getLogin()}:dk{$entity->getDragonkills()}-lv{$entity->getLevel()}");

    if (0 == $entity->getDragonkills())
    {
        $dk0lvl += $entity->getLevel();
        $dk0ct++;
    }
    elseif (1 == $entity->getDragonkills())
    {
        $dk1lvl += $entity->getLevel();
        $dk1ct++;
    }
    $dks += $entity->getDragonkills();
}

//Log which accounts were deleted.
$msg = "[{$dk0ct}] with 0 dk avg lvl [".round($dk0lvl / max(1, $dk0ct), 2)."]\n";
$msg .= "[{$dk1ct}] with 1 dk avg lvl [".round($dk1lvl / max(1, $dk1ct), 2)."]\n";
$msg .= 'Avg DK: ['.round($dks / max(1, count($result)), 2)."]\n";
$msg .= 'Accounts: '.implode(', ', $pinfo);

gamelog('Deleted '.count($result)." accounts:\n$msg", 'char expiration');

//adjust for notification - don't notify total newbie chars
$old = max(1, $old - (int) getsetting('notifydaysbeforedeletion', 5)); //a minimum of 1 day is necessary

$repository = \Doctrine::getRepository('LotgdCore:Accounts');
$query = $repository->createQueryBuilder('a');

$query
    ->where('BIT_AND(a.superuser, :permit) = 0')
    ->andWhere($expr->orX(
            '1 = 0',
            $old ? $expr->lt('a.laston', ':dateOld') : null
        ),
        $expr->andX(
            $expr->neq('a.emailaddress', ':empty'),
            $expr->eq('a.sentnotice', 0)
        )
    )
    ->leftJoin('LotgdCore:Characters', 'u', 'with', $expr->eq('u.id', 'a.character'))

    ->setParameter('permit', NO_ACCOUNT_EXPIRATION)
    ->setParameter('empty', '')
;

($old) ? $query->setParameter('dateOld', $dateOld) : null;

$result = $query->getQuery()->getResult();

$server = (string) getsetting('serverurl', 'http://nodomain.notd');
foreach ($result as $entity)
{
    $prefs = $entity->getPrefs();

    $subject = \LotgdTranslator::t('expirationnotice.subject', [], 'app-mail', $prefs['language'] ?? null);
    $message = \LotgdTranslator::t('expirationnotice.body', [
        'charname' => $entity->getLogin(),
        'server' => $server
    ], 'app-mail', $prefs['language'] ?? null);

    lotgd_mail($entity->getEmailaddress(), $subject, $message);

    $entity->setSentnotice(true);

    \Doctrine::persist($entity);
}

\Doctrine::flush();
