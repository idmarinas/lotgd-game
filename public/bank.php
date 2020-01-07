<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/systemmail.php';
require_once 'lib/sanitize.php';

$result = modulehook('bank-text-domain', ['textDomain' => 'page-bank', 'textDomainNavigation' => 'navigation-bank']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];

page_header('title', [], $textDomain);

$op = \LotgdHttp::getQuery('op');

$params = [
    'textDomain' => $textDomain,
    'ownerName' => getsetting('bankername', '`@Elessa`0')
];

if ('transfer' == $op)
{
    $params['opt'] = 'transfer';
    $params['transferPerLevel'] = getsetting('transferperlevel', 25);
    $params['maxTransfer'] = $session['user']['level'] * getsetting('maxtransferout', 25);
}
elseif ('transfer2' == $op)
{
    $to = \LotgdHttp::getPost('to');
    $amt = abs((int) \LotgdHttp::getPost('amount', 0));
    $params['opt'] = 'transfer2';
    $params['amount'] = $amt;
    $params['to'] = $to;

    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);
    $characters = $repository->findLikeName("%{$to}%", 100);

    $params['characters'] = $characters;
}
elseif ('transfer3' == $op)
{
    $amt = abs((int) \LotgdHttp::getPost('amount'));
    $to = (int) \LotgdHttp::getPost('to');
    $maxout = $session['user']['level'] * getsetting('maxtransferout', 25);
    $params['opt'] = 'transfer3';
    $params['maxOut'] = $maxout;
    $params['amount'] = $amt;

    $params['transferred'] = false;

    if ($to == $session['user']['acctid'])
    {
        $params['transferred'] = 'sameAct';
    }
    elseif (($session['user']['amountouttoday'] + $amt) > $maxout)
    {
        $params['transferred'] = 'maxOut';
    }
    elseif ($amt < (int) $session['user']['level'])
    {
        $params['transferred'] = 'level';
    }
    elseif (($session['user']['gold'] + $session['user']['goldinbank']) >= $amt)
    {
        $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);
        $result = $repository->find($to);

        $params['transferred'] = 0;
        if ($result)
        {
            $maxtfer = $result->getLevel() * getsetting('transferperlevel', 25);

            if ($result->getTransferredtoday() >= getsetting('transferreceive', 3))
            {
                $params['transferred'] = 'tomanytfer';
                $params['name'] = $result->getName();
            }
            elseif ($maxtfer < $amt)
            {
                $params['transferred'] = 'maxtfer';
                $params['maxtfer'] = $maxtfer;
                $params['name'] = $result->getName();
            }
            else
            {
                $params['transferred'] = true;
                $session['user']['gold'] -= $amt;

                if ($session['user']['gold'] < 0)
                {
                    //withdraw in case they don't have enough on hand.
                    $session['user']['goldinbank'] += $session['user']['gold'];
                    $session['user']['gold'] = 0;
                }
                $session['user']['amountouttoday'] += $amt;

                $result->setGoldinbank($result->getGoldinbank() + $amt);
                $result->setTransferredtoday($result->getTransferredtoday() + 1);

                \Doctrine::persist($result);
                \Doctrine::flush();

                debuglog("transferred $amt gold to", $result->getAcct()->getAcctid());

                $subj = ['transfer3.success.mail.subject', [], $textDomain];
                $body = ['transfer3.success.mail.message', ['name' => $session['user']['name'], 'amount' => $amt], $textDomain];

                systemmail($result->getAcct()->getAcctid(), $subj, $body);
            }
        }
    }
}
elseif ('deposit' == $op)
{
    $params['opt'] = 'deposit';
}
elseif ('depositfinish' == $op)
{
    $amount = abs((int) \LotgdHttp::getPost('amount'));
    $amount = (0 == $amount) ? $session['user']['gold'] : $amount;

    $params['amount'] = $amount;
    $params['deposited'] = false;
    $params['opt'] = 'depositend';

    if ($amount <= $session['user']['gold'])
    {
        $params['deposited'] = true;
        debuglog('deposited '.$amount.' gold in the bank');
        $session['user']['goldinbank'] += $amount;
        $session['user']['gold'] -= $amount;
    }
}
elseif ('borrow' == $op)
{
    $maxborrow = $session['user']['level'] * getsetting('borrowperlevel', 20);

    $params['opt'] = 'borrow';
    $params['maxborrow'] = $maxborrow;
}
elseif ('withdraw' == $op)
{
    $params['opt'] = 'withdraw';
}
elseif ('withdrawfinish' == $op)
{
    $amount = abs((int) \LotgdHttp::getPost('amount'));
    $amount = (0 == $amount) ? $session['user']['goldinbank'] : $amount;

    $params['opt'] = 'withdrawend';
    $params['amount'] = $amount;
    $params['withdrawal'] = false;

    if ($amount > $session['user']['goldinbank'] && '' != \LotgdHttp::getPost('borrow'))
    {
        $lefttoborrow = $amount;
        $maxborrow = $session['user']['level'] * getsetting('borrowperlevel', 20);
        $params['withdrawal'] = 1;
        $params['lefttoborrow'] = $lefttoborrow;
        $params['maxborrow'] = $maxborrow;
        $params['borrowed'] = false;
        $params['didwithdraw'] = false;

        if ($lefttoborrow <= ($session['user']['goldinbank'] + $maxborrow))
        {
            $params['withdrawal'] = 2;

            if ($session['user']['goldinbank'] > 0)
            {
                $params['goldInBank'] = $session['user']['goldinbank'];
                $params['didwithdraw'] = true;
                $lefttoborrow -= $session['user']['goldinbank'];
                $session['user']['gold'] += $session['user']['goldinbank'];
                $session['user']['goldinbank'] = 0;

                debuglog("withdrew $amount gold from the bank");
            }

            $params['lefttoborrow'] = $lefttoborrow;
            if (($lefttoborrow - $session['user']['goldinbank']) <= $maxborrow)
            {
                $params['borrowed'] = true;
                $session['user']['goldinbank'] -= $lefttoborrow;
                $session['user']['gold'] += $lefttoborrow;

                debuglog("borrows $lefttoborrow gold from the bank");
            }
        }
    }
    elseif ($amount <= $session['user']['goldinbank'])
    {
        $params['withdrawal'] = true;
        $session['user']['goldinbank'] -= $amount;
        $session['user']['gold'] += $amount;

        debuglog("withdrew $amount gold from the bank");
    }
}

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::villageNav();
\LotgdNavigation::addHeader('category.money');

if ($session['user']['goldinbank'] >= 0)
{
    \LotgdNavigation::addNav('nav.withdraw', 'bank.php?op=withdraw');
    \LotgdNavigation::addNav('nav.deposit.label', 'bank.php?op=deposit');

    if (getsetting('borrowperlevel', 20))
    {
        \LotgdNavigation::addNav('nav.borrow.label', 'bank.php?op=borrow');
    }
}
else
{
    \LotgdNavigation::addNav('nav.deposit.pay', 'bank.php?op=deposit');

    if (getsetting('borrowperlevel', 20))
    {
        \LotgdNavigation::addNav('nav.borrow.more', 'bank.php?op=borrow');
    }
}

\LotgdNavigation::addNav('nav.transfer', 'bank.php?op=transfer');
if (getsetting('allowgoldtransfer', 1) && ($session['user']['level'] >= getsetting('mintransferlev', 3) || $session['user']['dragonkills'] > 0))
{
    \LotgdNavigation::addNav('nav.transfer', 'bank.php?op=transfer');
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-bank-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/bank.twig', $params));

page_footer();
