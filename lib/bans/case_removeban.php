<?php

use Lotgd\Core\Entity\Bans;
use Doctrine\ORM\Query\Expr\Join;

$repository = Doctrine::getRepository(Bans::class);

if ('delban' == $op)
{
    $ip = LotgdRequest::getQuery('ipfilter');
    $id = LotgdRequest::getQuery('uniqueid');

    if ($repository->deleteBan($ip, $id))
    {
        LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('removeban.delban', ['ip' => $ip, 'id' => $id], $textDomain));
    }
}

//-- Delete expire bans
$removed = $repository->removeExpireBans();

if ($removed)
{
    LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('removeban.expired', ['count' => $removed], $textDomain));
}

$page      = (int) LotgdRequest::getQuery('page');
$duration  = (string) LotgdRequest::getQuery('duration');
$duration  = $duration ?: 'P14D';
$notBefore = (int) LotgdRequest::getQuery('notbefore');
$operator  = $notBefore !== 0 ? '>=' : '<=';

$date  = new DateTime('now');
$query = $repository->createQueryBuilder('u');
$query->orderBy('u.banexpire', 'ASC');

if ('searchban' == $op && $target)
{
    $params['showing'] = ['removeban.showing.search', ['name' => $target]];

    $repositoryChar = Doctrine::getRepository('LotgdCore:Avatar');
    $query          = $repositoryChar->createQueryBuilder('u');
    $expr           = $query->expr();

    $query->select('b')
        ->join(
            'LotgdCore:User',
            'a',
            Join::WITH,
            $expr->eq('a.acctid', 'u.acct')
        )
        ->join(
            Bans::class,
            'b',
            Join::WITH,
            $expr->orX($expr->like('b.ipfilter', 'a.lastip'), $expr->like('b.uniqueid', 'a.uniqueid'))
        )
        ->where('u.name LIKE :name')
        ->setParameter('name', "%{$target}%")
        ->orderBy('b.banexpire', 'ASC')
    ;
}
elseif ('forever' !== $duration && 'all' !== $duration)
{
    $type   = \substr($duration, -1);
    $matchs = [];
    \preg_match('/[[:digit:]]+/', $duration, $matchs);
    $count = $matchs[0];

    if ('D' == $type)
    {
        $count /= 7;
    }
    $params['showing'] = ["removeban.showing.{$type}", ['notBefore' => $notBefore, 'n' => $count]];

    $query->where("u.banexpire {$operator} :date AND u.banexpire > '0000-00-00 00:00:00'")
        ->setParameter('date', $date->add(new DateInterval($duration)))
    ;
}
elseif ('forever' === $duration)
{
    $query->where("u.banexpire = '0000-00-00 00:00:00'");
    $params['showing'] = 'removeban.showing.perma';
}
elseif ('all' === $duration)
{
    $params['showing'] = 'removeban.showing.all';
}

$params['paginator'] = $repository->getPaginator($query, $page, 35);

LotgdNavigation::addHeader('bans.category.perma');
LotgdNavigation::addNav('bans.nav.show', 'bans.php?op=removeban&duration=forever');

LotgdNavigation::addHeader('bans.category.expire.within');
LotgdNavigation::addNav('bans.nav.week', 'bans.php?op=removeban&duration=P7D', ['params' => ['n' => 1]]);
LotgdNavigation::addNav('bans.nav.week', 'bans.php?op=removeban&duration=P14D', ['params' => ['n' => 2]]);
LotgdNavigation::addNav('bans.nav.week', 'bans.php?op=removeban&duration=P21D', ['params' => ['n' => 3]]);
LotgdNavigation::addNav('bans.nav.week', 'bans.php?op=removeban&duration=P28D', ['params' => ['n' => 4]]);

LotgdNavigation::addNav('bans.nav.month', 'bans.php?op=removeban&duration=P2M', ['params' => ['n' => 2]]);
LotgdNavigation::addNav('bans.nav.month', 'bans.php?op=removeban&duration=P3M', ['params' => ['n' => 3]]);
LotgdNavigation::addNav('bans.nav.month', 'bans.php?op=removeban&duration=P4M', ['params' => ['n' => 4]]);
LotgdNavigation::addNav('bans.nav.month', 'bans.php?op=removeban&duration=P5M', ['params' => ['n' => 5]]);
LotgdNavigation::addNav('bans.nav.month', 'bans.php?op=removeban&duration=P6M', ['params' => ['n' => 6]]);

LotgdNavigation::addNav('bans.nav.year', 'bans.php?op=removeban&duration=P1Y', ['params' => ['n' => 1]]);
LotgdNavigation::addNav('bans.nav.year', 'bans.php?op=removeban&duration=P2Y', ['params' => ['n' => 2]]);
LotgdNavigation::addNav('bans.nav.year', 'bans.php?op=removeban&duration=P4Y', ['params' => ['n' => 4]]);

LotgdNavigation::addNav('bans.nav.all', 'bans.php?op=removeban&duration=all');

LotgdNavigation::addHeader('bans.category.expire.notBefore');
LotgdNavigation::addNav('bans.nav.week', 'bans.php?op=removeban&duration=P7D&notbefore=1', ['params' => ['n' => 1]]);
LotgdNavigation::addNav('bans.nav.week', 'bans.php?op=removeban&duration=P14D&notbefore=1', ['params' => ['n' => 2]]);
LotgdNavigation::addNav('bans.nav.week', 'bans.php?op=removeban&duration=P21D&notbefore=1', ['params' => ['n' => 3]]);
LotgdNavigation::addNav('bans.nav.week', 'bans.php?op=removeban&duration=P28D&notbefore=1', ['params' => ['n' => 4]]);

LotgdNavigation::addNav('bans.nav.month', 'bans.php?op=removeban&duration=P2M&notbefore=1', ['params' => ['n' => 2]]);
LotgdNavigation::addNav('bans.nav.month', 'bans.php?op=removeban&duration=P3M&notbefore=1', ['params' => ['n' => 3]]);
LotgdNavigation::addNav('bans.nav.month', 'bans.php?op=removeban&duration=P4M&notbefore=1', ['params' => ['n' => 4]]);
LotgdNavigation::addNav('bans.nav.month', 'bans.php?op=removeban&duration=P5M&notbefore=1', ['params' => ['n' => 5]]);
LotgdNavigation::addNav('bans.nav.month', 'bans.php?op=removeban&duration=P6M&notbefore=1', ['params' => ['n' => 6]]);

LotgdNavigation::addNav('bans.nav.year', 'bans.php?op=removeban&duration=P1Y&notbefore=1', ['params' => ['n' => 1]]);
LotgdNavigation::addNav('bans.nav.year', 'bans.php?op=removeban&duration=P2Y&notbefore=1', ['params' => ['n' => 2]]);
LotgdNavigation::addNav('bans.nav.year', 'bans.php?op=removeban&duration=P4Y&notbefore=1', ['params' => ['n' => 4]]);
