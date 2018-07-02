<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/systemmail.php';
require_once 'lib/superusernav.php';

check_su_access(SU_EDIT_DONATIONS);

tlschema('donation');

page_header("Donator's Page");

$op = httpget('op');
$ret = httpget('ret');
$return = cmd_sanitize($ret);
$return = substr($return, strrpos($return, '/') + 1);

$name = httppost('name') ?: httpget('name');
$amt = httppost('amt') ?: httpget('amt');
$reason = httppost('reason') ?: httpget('reason');
$reason = $reason ?: 'manual donation entry';
$txnid = httppost('txnid') ?: httpget('txnid');

superusernav();
tlschema('nav');
addnav('Return whence you came', $return);
tlschema();

$twig = [
    'data' => compact('name', 'amt', 'reason', 'txnid'),
    'ret' => rawurlencode($ret)
];

rawoutput($lotgd_tpl->renderThemeTemplate('pages/donators/add.twig', $twig));

addnav('Donations');
if (($session['user']['superuser'] & SU_EDIT_PAYLOG) && file_exists('paylog.php'))
{
    addnav('Payment Log', 'paylog.php');
}

if ('add2' == $op)
{
    $id = httpget('id');
    $amt = httpget('amt');
    $reason = httpget('reason');

    $sql = 'SELECT name FROM '.DB::prefix('accounts')." WHERE acctid=$id;";
    $result = DB::query($sql);
    $row = DB::fetch_assoc($result);
    output('%s donation points added to %s`0, reason: `^%s`0', $amt, $row['name'], $reason);

    $txnid = httpget('txnid');
    $ret = httpget('ret');

    if ($id == $session['user']['acctid'])
    {
        $session['user']['donation'] += $amt;
    }

    if ($txnid > '')
    {
        $result = modulehook('donation_adjustments', ['points' => $amt, 'amount' => $amt / getsetting('dpointspercurrencyunit', 100), 'acctid' => $id, 'messages' => []]);
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
    else
    {
        $points = $amt;
    }
    // ok to execute when this is the current user, they'll overwrite the
    // value at the end of their page hit, and this will allow the display
    // table to update in real time.
    $sql = 'UPDATE '.DB::prefix('accounts')." SET donation=donation+'$points' WHERE acctid='$id'";
    DB::query($sql);
    modulehook('donation', ['id' => $id, 'amt' => $points, 'manual' => ($txnid > '' ? false : true)]);

    if ($txnid > '')
    {
        $sql = 'UPDATE '.DB::prefix('paylog')." SET acctid='$id', processed=1 WHERE txnid='$txnid'";
        DB::query($sql);
        debuglog("Received donator points for donating -- Credited manually [$reason]", false, $id, 'donation', $points, false);
        redirect('paylog.php');
    }
    else
    {
        debuglog("Received donator points -- Manually assigned, not based on a known dollar donation [$reason]", false, $id, 'donation', $amt, false);
    }

    if (1 == $points)
    {
        systemmail($id, ['Donation Point Added'], ['`2You have received a donation point for %s.', $reason]);
    }
    else
    {
        systemmail($id, ['Donation Points Added'], ['`2You have received %d donation points for %s.', $points, $reason]);
    }
    httpset('op', '');
    $op = '';
}

if ('' == $op)
{
    $sql = 'SELECT name,donation,donationspent FROM '.DB::prefix('accounts').' WHERE donation>0 ORDER BY donation DESC LIMIT 25';
    $result = DB::query($sql);

    $twig = [
        'content' => $result
    ];

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/donators.twig', $twig));
}
elseif ('add1' == $op)
{
    $search = '%';
    $search = str_replace("'", "\'", $name);

    $select = DB::select('accounts');
    $select->columns(['name', 'acctid', 'donation', 'donationspent'])
        ->limit(100)
        ->where->like('login', "%$search%")
            ->like('name', "%$search%")
    ;
    $result = DB::execute($select);

    //-- If not found accounts select first 100 accounts in DB
    if (! $result->count())
    {
        $select = DB::select('accounts');
        $select->columns(['name', 'acctid', 'donation', 'donationspent'])
            ->limit(100)
        ;
        $result = DB::execute($select);
    }

    $twig = [
        'content' => $result,
        'amt' => $amt,
        'reason' => $reason,
        'txnid' => $txnid
    ];

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/donators/confirm.twig', $twig));
}

page_footer();
