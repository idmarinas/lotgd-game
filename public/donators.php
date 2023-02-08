<?php

use Doctrine\ORM\Query\Expr\Join;
use Lotgd\Core\Entity\Paylog;
// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

check_su_access(SU_EDIT_DONATIONS);

$textDomain = 'grotto_donators';

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
];

$op               = (string) LotgdRequest::getQuery('op');
$page             = (int) LotgdRequest::getQuery('page');
$acctRepository   = Doctrine::getRepository('LotgdCore:User');
$paylogRepository = Doctrine::getRepository(Paylog::class);

$name           = (string) LotgdRequest::getPost('name');
$name           = $name ?: (string) LotgdRequest::getQuery('name');
$params['name'] = $name;

$amt           = (int) LotgdRequest::getPost('amt');
$amt           = $amt ?: (int) LotgdRequest::getQuery('amt');
$params['amt'] = $amt;

$reason           = (string) LotgdRequest::getPost('reason');
$reason           = $reason ?: (string) LotgdRequest::getQuery('reason');
$reason           = $reason ?: LotgdTranslator::t('form.value.reason', [], $textDomain);
$params['reason'] = $reason;

$txnid           = (string) LotgdRequest::getPost('txnid');
$txnid           = $txnid ?: (string) LotgdRequest::getQuery('txnid');
$params['txnid'] = $txnid;

LotgdNavigation::superuserGrottoNav();
LotgdNavigation::addNav('donators.nav.refresh', 'donators.php');

if ('save' == $op)
{
    $id = (int) LotgdRequest::getQuery('id');

    $account = $acctRepository->find($id);

    if ($id == $session['user']['acctid'])
    {
        $session['user']['donation'] += $amt;
    }

    $points = $amt;
    if ('' !== $txnid && '0' !== $txnid)
    {
        $args = new GenericEvent(null, [
            'points'   => $amt,
            'amount'   => $amt / LotgdSetting::getSetting('dpointspercurrencyunit', 100),
            'acctid'   => $id,
            'messages' => [],
        ]);
        LotgdEventDispatcher::dispatch($args, Events::PAYMENT_DONATION_ADJUSTMENT);
        $result = $args->getArguments();
        $points = $result['points'];

        if ( ! \is_array($result['messages']))
        {
            $result['messages'] = [$result['messages']];
        }

        foreach ($result['messages'] as $message)
        {
            LotgdLog::debug($message, false, $id, 'donation', 0, false);
        }
    }

    // ok to execute when this is the current user, they'll overwrite the
    // value at the end of their page hit, and this will allow the display
    // table to update in real time.
    $account->setDonation($account->getDonation() + $points);
    Doctrine::persist($account);
    Doctrine::flush();

    $args = new GenericEvent(null, [
        'id'     => $id,
        'amt'    => $points,
        'manual' => (bool) ($txnid),
    ]);
    LotgdEventDispatcher::dispatch($args, Events::PAYMENT_DONATION_SUCCESS);

    if ('' !== $txnid && '0' !== $txnid)
    {
        $paylog = $paylogRepository->findOneByTxnid($txnid);

        $paylog->setAcctid($id)
            ->setProcessed(1)
        ;

        Doctrine::persist($paylog);
        Doctrine::flush();

        LotgdLog::debug("Received donator points for donating -- Credited manually [{$reason}]", false, $id, 'donation', $points, false);

        redirect('paylog.php');
    }
    else
    {
        LotgdLog::debug("Received donator points -- Manually assigned, not based on a known dollar donation [{$reason}]", false, $id, 'donation', $amt, false);
    }

    $subj = ['mail.donation.subject', ['points' => $points], $textDomain];
    $msg  = ['mail.donation.message', [
        'points' => $points,
        'reason' => $reason,
    ], $textDomain];

    LotgdKernel::get('lotgd_core.tool.system_mail')->send($id, $subj, $msg);

    LotgdFlashMessages::addSuccessMessage(LotgdTranslator::t('flash.message.donation', [
        'name'   => $account->getCharacter()->getName(),
        'points' => $points,
        'reason' => $reason,
    ], $textDomain));

    $op = '';
}

if ('add' == $op)
{
    $params['tpl'] = 'add';

    $query = $acctRepository->createQueryBuilder('u');
    $expr  = $query->expr();

    $query->select('u.acctid', 'u.donation', 'u.donationspent')
        ->addSelect('c.name')
        ->join(
            'LotgdCore:Avatar',
            'c',
            Join::WITH,
            $expr->eq('c.id', 'u.avatar')
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
    $expr  = $query->expr();

    $query->select('u.donation', 'u.donationspent')
        ->addSelect('c.name')
        ->join(
            'LotgdCore:Avatar',
            'c',
            Join::WITH,
            $expr->eq('c.id', 'u.avatar')
        )
        ->where('u.donation > 0')
        ->orderBy('u.donation', 'DESC')
    ;

    $params['paginator'] = $acctRepository->getPaginator($query, $page);
}

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/donators.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();
