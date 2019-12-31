<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/systemmail.php';

check_su_access(SU_EDIT_DONATIONS);

$textDomain = 'grotto-donators';

page_header('title', [], $textDomain);

$params = [];
$op = (string) \LotgdHttp::getQuery('op');
$page = (int) \LotgdHttp::getQuery('page');
$acctRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);
$paylogRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Paylog::class);

$name = (string) \LotgdHttp::getPost('name');
$name = $name ?: (string) \LotgdHttp::getQuery('name');
$params['name'] = $name;

$amt = (int) \LotgdHttp::getPost('amt');
$amt = $amt ?: (int) \LotgdHttp::getQuery('amt');
$params['amt'] = $amt;

$reason = (string) \LotgdHttp::getPost('reason');
$reason = $reason ?: (string) \LotgdHttp::getQuery('reason');
$reason = $reason ?: \LotgdTranslator::t('form.value.reason', [], $textDomain);
$params['reason'] = $reason;

$txnid = (string) \LotgdHttp::getPost('txnid');
$txnid = $txnid ?: (string) \LotgdHttp::getQuery('txnid');
$params['txnid'] = $txnid;

\LotgdNavigation::superuserGrottoNav();
\LotgdNavigation::addNav('donators.nav.refresh', 'donators.php');

if ('save' == $op)
{
    $id = (int) \LotgdHttp::getQuery('id');

    $account = $acctRepository->find($id);

    if ($id == $session['user']['acctid'])
    {
        $session['user']['donation'] += $amt;
    }

    $points = $amt;
    if ($txnid)
    {
        $result = modulehook('donation_adjustments', [
            'points' => $amt,
            'amount' => $amt / getsetting('dpointspercurrencyunit', 100),
            'acctid' => $id,
            'messages' => []
        ]);
        $points = $result['points'];

        if (! is_array($result['messages']))
        {
            $result['messages'] = [$result['messages']];
        }

        foreach ($result['messages'] as $messageid => $message)
        {
            debuglog($message, false, $id, 'donation', 0, false);
        }
    }

    // ok to execute when this is the current user, they'll overwrite the
    // value at the end of their page hit, and this will allow the display
    // table to update in real time.
    $account->setDonation($account->getDonation() + $points);
    \Doctrine::persist($account);
    \Doctrine::flush();

    modulehook('donation', [
        'id' => $id,
        'amt' => $points,
        'manual' => (bool) ($txnid)
    ]);

    if ($txnid)
    {
        $paylog = $paylogRepository->findOneByTxnid($txnid);

        $paylog->setAcctid($id)
            ->setProcessed(1)
        ;

        \Doctrine::persist($paylog);
        \Doctrine::flush();

        debuglog("Received donator points for donating -- Credited manually [$reason]", false, $id, 'donation', $points, false);

        return redirect('paylog.php');
    }
    else
    {
        debuglog("Received donator points -- Manually assigned, not based on a known dollar donation [$reason]", false, $id, 'donation', $amt, false);
    }

    $subj = ['mail.donation.subject', ['points' => $points], $textDomain];
    $msg = ['mail.donation.message', [
        'points' => $points,
        'reason' => $reason
    ], $textDomain];

    systemmail($id, $subj, $msg);

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.donation', [
        'name' => $account->getCharacter()->getName(),
        'points' => $points,
        'reason' => $reason
    ], $textDomain));

    $op = '';
}


if('add' == $op)
{
    $params['tpl'] = 'add';

    $query = $acctRepository->createQueryBuilder('u');
    $expr = $query->expr();

    $query->select('u.acctid', 'u.donation', 'u.donationspent')
        ->addSelect('c.name')
        ->join(
            \Lotgd\Core\Entity\Characters::class,
            'c',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            $expr->eq('c.id', 'u.character')
        )
        ->where('u.login LIKE :name OR c.name LIKE :name')
        ->setParameter('name', "%{$name}%")
    ;

    $params['paginator'] = $acctRepository->getPaginator($query, $page);
}
elseif ('' == $op || $op)
{
    $params['tpl'] = 'default';

    $query = $acctRepository->createQueryBuilder('u');
    $expr = $query->expr();

    $query->select('u.donation', 'u.donationspent')
        ->addSelect('c.name')
        ->join(
            \Lotgd\Core\Entity\Characters::class,
            'c',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            $expr->eq('c.id', 'u.character')
        )
        ->where('u.donation > 0')
        ->orderBy('u.donation', 'DESC')
    ;

    $params['paginator'] = $acctRepository->getPaginator($query, $page);
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/donators.twig', $params));

page_footer();
