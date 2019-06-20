<?php

// mail ready
// addnews ready
// translator ready
ob_start();

set_error_handler('payment_error');
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

tlschema('payment');

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

$post = \LotgdHttp::getPostAll();
reset($post);

foreach ($post as $key => $value)
{
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}

// post back to PayPal system to validate
$header = '';
$header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
$header .= 'Content-Length: '.strlen($req)."\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Host: www.paypal.com\r\n";
$header .= "Connection: close\r\n\r\n";

$fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = \LotgdHttp::getPost('item_name');
$item_number = \LotgdHttp::getPost('item_number');
$payment_status = \LotgdHttp::getPost('payment_status');
$payment_amount = \LotgdHttp::getPost('mc_gross');
$payment_currency = \LotgdHttp::getPost('mc_currency');
$txn_id = \LotgdHttp::getPost('txn_id');
$receiver_email = \LotgdHttp::getPost('business');
$payer_email = \LotgdHttp::getPost('payer_email');
$payment_fee = \LotgdHttp::getPost('mc_fee');

$response = '';

if (! $fp)
{
    // HTTP ERROR
    payment_error(E_ERROR, 'Unable to open socket to verify payment', __FILE__, __LINE__);
}
else
{
    fputs($fp, $header.$req);

    $repository = \Doctrine::getRepository('LotgdCore:Paylog');

    while (! feof($fp))
    {
        $res = fgets($fp, 1024);
        $response .= $res;

        if (0 == strcmp(trim($res), 'VERIFIED'))
        {
            // check the payment_status is Completed
            // check that txn_id has not been previously processed
            // check that receiver_email is your Primary PayPal email
            // check that payment_amount/payment_currency are correct
            // process payment
            if ('Completed' == $payment_status || 'Refunded' == $payment_status)
            {
                if ('Refunded' == $payment_status)
                {
                    //sanitize the data to look like a completed transaction
                    $payment_amount = $mc_gross;
                    $payment_fee = 0;
                    $txn_type = 'refund';
                }

                $result = $repository->findOneBy([ 'txnid' => $txn_id ]);

                if ($result)
                {
                    $emsg .= "Already logged this transaction ID ($txn_id)\n";
                    payment_error(E_ERROR, $emsg, __FILE__, __LINE__);
                }

                if (('logd@mightye.org' != $receiver_email) &&
                    ($receiver_email != getsetting('paypalemail', '')))
                {
                    $emsg = "This payment isn't to me!  It's to $receiver_email.\n";
                    payment_error(E_WARNING, $emsg, __FILE__, __LINE__);
                }
                writelog($response);
            }
            else
            {
                modulehook('donation-error', $post);
                payment_error(E_ERROR, "Payment Status isn't 'Completed' it's '$payment_status'", __FILE__, __LINE__);
            }
        }
        elseif (0 == strcmp(trim($res), 'INVALID'))
        {
            // log for manual investigation
            payment_error(E_ERROR, "Payment Status is 'INVALID'!\n\nPOST data:`n".serialize($_POST), __FILE__, __LINE__);
        }
    }
    fclose($fp);
}

function writelog($response)
{
    global $post;
    global $item_name, $item_number, $payment_status, $payment_amount;
    global $payment_currency, $txn_id, $receiver_email, $payer_email;
    global $payment_fee, $txn_type;

    $match = [];
    preg_match("'([^:]*):([^/])*'", $item_number, $match);

    if ($match[1] > '')
    {
        $match[1] = addslashes($match[1]);

        $repository = \Doctrine::getRepository('LotgdCore:Accounts');
        $account = $repository->findOneBy([ 'login' => $match[1] ]);
        $acctId = 0;

        if ($account)
        {
            $acctId = $account->getAcctid();
            $donation = $payment_amount;

            // if it's a reversal, it'll only post back to us the amount
            // we received back, with out counting the fees, which we
            // receive under a different transaction, but get no
            // notification for.
            if ('reversal' == $txn_type)
            {
                $donation -= $payment_fee;
            }

            $hookresult = modulehook('donation_adjustments', [
                'points' => $donation * (int) getsetting('dpointspercurrencyunit', 100),
                'amount' => $donation,
                'acctid' => $acctId,
                'messages' => []
            ]);
            $hookresult['points'] = round($hookresult['points']);

            $account->setDonation($account->getDonation() + $hookresult['points']);
            \Doctrine::persist($account);

            debuglog('Received donator points for donating -- Credited Automatically', false, $acctId, 'donation', $hookresult['points'], false);

            if (! is_array($hookresult['messages']))
            {
                $hookresult['messages'] = [$hookresult['messages']];
            }

            foreach ($hookresult['messages'] as $id => $message)
            {
                debuglog($message, false, $acctId, 'donation', 0, false);
            }

            if (DB::affected_rows() > 0)
            {
                $processed = 1;
            }
            modulehook('donation', ['id' => $acctId, 'amt' => $donation * getsetting('dpointspercurrencyunit', 100), 'manual' => false]);
        }
    }

    $paylogRepository = \Doctrine::getRepository('LotgdCore:Paylog');
    $paylogEntity = $paylogRepository->hydrateEntity([
        'info' => $post,
        'response' => $response,
        'txnid' => $txn_id,
        'amount' => $payment_amount,
        'name' => $match[1],
        'acctid' => $acctId,
        'processed' => $processed ?? 0,
        'filed' => 0,
        'txfee' => $payment_fee,
        'processdate' => new \DateTime('now')

    ]);
    \Doctrine::persist($paylogEntity);
    \Doctrine::flush();
}

function payment_error($errno, $errstr, $errfile, $errline)
{
    global $payment_errors;

    if (! is_int($errno) || (is_int($errno) && ($errno & error_reporting())))
    {
        $payment_errors .= "Error $errno: $errstr in $errfile on $errline\n";
    }
}

$adminEmail = getsetting('gameadminemail', 'postmaster@localhost.com') ?: 'trash@mightye.org';

if ($payment_errors > '')
{
    // $payment_errors not translated
    ob_start();
    echo '<b>GET:</b><pre>';
    reset($_GET);
    var_dump($_GET);
    echo '</pre><b>POST:</b><pre>';
    reset($_POST);
    var_dump($_POST);
    echo '</pre><b>SERVER:</b><pre>';
    reset($_SERVER);
    var_dump($_SERVER);
    echo '</pre>';
    $contents = ob_get_contents();
    ob_end_clean();
    $payment_errors .= '<hr>'.$contents;

    mail($adminEmail, 'Payment Error', $payment_errors.'<hr>', 'From: '.getsetting('gameadminemail', 'postmaster@localhost.com'));
}
$output = ob_get_contents();

if ($output > '')
{
    echo '<b>GET:</b><pre>';
    reset($_GET);
    var_dump($_GET);
    echo '</pre><b>POST:</b><pre>';
    reset($_POST);
    var_dump($_POST);
    echo '</pre><b>SERVER:</b><pre>';
    reset($_SERVER);
    var_dump($_SERVER);
    echo '</pre>';
    mail($adminEmail, "Serious LoGD Payment Problems on {$_SERVER['HTTP_HOST']}", ob_get_contents(), 'Content-Type: text/html');
}

ob_end_clean();
