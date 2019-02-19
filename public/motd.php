<?php

// addnews ready
// translator ready
// mail ready
define('ALLOW_ANONYMOUS', true);
define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';
require_once 'lib/commentary.php';
require_once 'lib/nltoappon.php';

use Doctrine\ORM\Query\Expr\Join;

addcommentary();

tlschema('motd');

popup_header('title', [], 'page-motd');

$op = (string) \LotgdHttp::getQuery('op', '');
$id = (int) \LotgdHttp::getQuery('id', 0);
$motdPerPage = (int) getsetting('motditems', 5);
$page = (int) \LotgdHttp::getQuery('page', 1);
$month = (string) \LotgdHttp::getPost('month', '');
$loggedin = $session['user']['loggedin'] ?? false;
$params = [];
$params['SU_POST_MOTD'] = ($session['user']['superuser'] & SU_POST_MOTD);
bdump($op);

if ('vote' == $op)
{
    if (! $loggedin)
    {
        return redirect('motd.php');
    }

    $motditem = (int) \LotgdHttp::getPost('motditem', 0);
    $choice = \LotgdHttp::getPost('choice', null);

    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Pollresults::class);
    $pollResult = new \Lotgd\Core\Entity\Pollresults();

    $pollResult->setChoice($choice)
        ->setAccount($session['user']['acctid'])
        ->setMotditem($motditem)
    ;

    \Doctrine::persist($pollResult);
    \Doctrine::flush();
    \Doctrine::clear();

    invalidatedatacache("poll-$motditem");

    return redirect('motd.php');
}
elseif ('edit' == $op)
{
    checkSuPermission(SU_POST_MOTD, 'motd.php');

    $changeauthor = (string) \LotgdHttp::getPost('changeauthor', '');
    $changedate = (string) \LotgdHttp::getPost('changedate', '');
    $subject = (string) \LotgdHttp::getPost('subject', '');
    $body = (string) \LotgdHttp::getPost('body', '');
    $preview = (string) \LotgdHttp::getPost('preview', '');

    $params['changeauthor'] = $changeauthor;
    $params['changedate'] = $changedate;
    $params['preview'] = $preview;

    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Motd::class);
    $motd = $repository->getEditMotdItem($id);

    if (! $motd)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('item.edit.notFound', ['id' => $id], 'page-motd'));

        return redirect('motd.php');
    }

    $params['motd'] = $motd;
    if ($changeauthor || '' == $motd['motdauthorname'])
    {
        $motd['motdauthorname'] = $session['user']['name'];
        $motd['motdauthor'] = $session['user']['acctid'];
    }

    if ($changedate || ! isset($motd['motddate']) || '' == $motd['motddate'])
    {
        $motd['motddate'] = new \DateTime('now');
    }

    if (trim($body) && trim($subject) && ! $preview)
    {
        $hydrator = new \Zend\Hydrator\ClassMethods();
        $motd = $hydrator->hydrate($motd, new \Lotgd\Core\Entity\Motd());
        $motd->setMotdbody($body)
            ->setMotdtitle($subject)
        ;

        \Doctrine::merge($motd);
        \Doctrine::flush();
        \Doctrine::clear();

        return redirect('motd.php');
    }

    $params = modulehook('page-motd-edit-tpl-params', $params);
    rawoutput(\LotgdTheme::renderThemeTemplate('pages/motd/edit/item.twig', $params));

    popup_footer();
}
elseif('add' == $op)
{
    checkSuPermission(SU_POST_MOTD, 'motd.php');

    $changeauthor = (string) \LotgdHttp::getPost('changeauthor', '');
    $changedate = (string) \LotgdHttp::getPost('changedate', '');
    $subject = (string) \LotgdHttp::getPost('subject', '');
    $body = (string) \LotgdHttp::getPost('body', '');
    $preview = (string) \LotgdHttp::getPost('preview', '');

    $params['changeauthor'] = $changeauthor;
    $params['changedate'] = $changedate;
    $params['preview'] = $preview;

    $motd = [
        'motdbody' => $body,
        'motdtitle' => $subject,
        'motdauthor' => $session['user']['acctid'],
        'motdauthorname' => $session['user']['name'],
        'motddate' => new \DateTime('now')
    ];

    $params['motd'] = $motd;
    if (trim($body) && trim($subject) && ! $preview)
    {
        $hydrator = new \Zend\Hydrator\ClassMethods();
        $motd = $hydrator->hydrate($motd, new \Lotgd\Core\Entity\Motd());
        $motd->setMotdbody($body)
            ->setMotdtitle($subject)
        ;

        \Doctrine::merge($motd);
        \Doctrine::flush();
        \Doctrine::clear();

        return redirect('motd.php');
    }

    $params = modulehook('page-motd-add-tpl-params', $params);
    rawoutput(\LotgdTheme::renderThemeTemplate('pages/motd/add/item.twig', $params));

    popup_footer();
}
elseif ('addpoll' == $op)
{
    checkSuPermission(SU_POST_MOTD, 'motd.php');

    $subject = (string) \LotgdHttp::getPost('subject', '');
    $body = (string) \LotgdHttp::getPost('body', '');

    $params['changeauthor'] = $changeauthor;
    $params['changedate'] = $changedate;
    $params['preview'] = $preview;

    $motd = [
        'motdtitle' => $subject,
        'motdauthor' => $session['user']['acctid'],
        'motdauthorname' => $session['user']['name'],
        'motddate' => new \DateTime('now'),
        'motdtype' => true
    ];

    if (trim($body) && trim($subject))
    {
        $opt = \LotgdHttp::getPost('opt');

        $body = serialize(['body' => $body, 'opt' => $opt]);

        $hydrator = new \Zend\Hydrator\ClassMethods();
        $motd = $hydrator->hydrate($motd, new \Lotgd\Core\Entity\Motd());
        $motd->setMotdbody($body);

        \Doctrine::merge($motd);
        \Doctrine::flush();
        \Doctrine::clear();

        return redirect('motd.php');
    }

    $params = modulehook('page-motd-addpoll-tpl-params', $params);
    rawoutput(\LotgdTheme::renderThemeTemplate('pages/motd/add/poll.twig', $params));

    popup_footer();
}
elseif ('del' == $op)
{
    if (! $id)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('item.del.noId', [], 'page-motd'));

        return redirect('motd.php');
    }

    $q = \Doctrine::createQuery('DELETE FROM Lotgd\Core\Entity\Motd u WHERE u.motditem = :id');
    $q->setParameter('id', $id);
    $deleted = $q->execute();

    invalidatedatacache('motd');
    invalidatedatacache('lastmotd');
    invalidatedatacache('motddate');

    return redirect('motd.php');
}

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Motd::class);
$qb = $repository->createQueryBuilder('u');
$qb->select('u', 'c.name as motdauthorname')
    ->leftJoin(\Lotgd\Core\Entity\Accounts::class, 'a', Join::WITH, $qb->expr()->eq('a.acctid', 'u.motdauthor'))
    ->leftJoin(\Lotgd\Core\Entity\Characters::class, 'c', Join::WITH, $qb->expr()->eq('c.id', 'a.character'))
    ->orderBy('u.motddate', 'DESC')
;

if ($month)
{
    $params['monthSelected'] = $month;
    bdump($month);
    $month = \explode('-', $month);
    bdump($month);
    $qb->where('MONTH(u.motddate) = :month AND YEAR(u.motddate) = :year')
        ->setParameters([
            'month' => $month[1],
            'year' => $month[0]
        ])
    ;
}

$params['paginator'] = $repository->getPaginator($qb, $page, $motdPerPage);
$params['motdMothCountPerYear'] = $repository->getMonthCountPerYear();
$session['needtoviewmotd'] = false;

$sql = 'SELECT motddate FROM '.DB::prefix('motd').' ORDER BY motditem DESC LIMIT 1';
$result = DB::query($sql);
$row = DB::fetch_assoc($result);
$session['user']['lastmotd'] = new \DateTime($row['motddate']);

$params = modulehook('page-motd-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('pages/motd.twig', $params));

tlschema();

popup_footer();
