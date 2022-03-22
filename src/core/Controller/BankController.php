<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.4.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\SystemMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BankController extends AbstractController
{
    private $dispatcher;
    private $log;
    private $settings;
    private $navigation;
    private $response;
    private $systemMail;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Log $log,
        Settings $settings,
        Navigation $navigation,
        HttpResponse $response,
        SystemMail $systemMail
    ) {
        $this->dispatcher = $eventDispatcher;
        $this->log        = $log;
        $this->settings   = $settings;
        $this->navigation = $navigation;
        $this->response   = $response;
        $this->systemMail = $systemMail;
    }

    public function index(Request $request): Response
    {
        global $session;

        $args = new GenericEvent(null, ['textDomain' => 'page_bank', 'textDomainNavigation' => 'navigation_bank']);
        $this->dispatcher->dispatch($args, Events::PAGE_BANK_PRE);
        $result               = modulehook('bank-text-domain', $args->getArguments());
        $textDomain           = $result['textDomain'];
        $textDomainNavigation = $result['textDomainNavigation'];

        $this->response->pageTitle('title', [], $textDomain);

        $params = [
            'textDomain' => $textDomain,
            'ownerName'  => $this->settings->getSetting('bankername', '`@Elessa`0'),
        ];

        $op     = (string) $request->query->get('op');
        $method = method_exists($this, $op) ? $op : 'enter';

        $this->navigation->villageNav();

        //-- Change text domain for navigation
        $this->navigation->setTextDomain($textDomainNavigation);
        $this->navigation->addHeader('category.money');

        if ($session['user']['goldinbank'] >= 0)
        {
            $this->navigation->addNav('nav.withdraw', 'bank.php?op=withdraw');
            $this->navigation->addNav('nav.deposit.label', 'bank.php?op=deposit');

            if ('' !== $this->settings->getSetting('borrowperlevel', 20) && '0' !== $this->settings->getSetting('borrowperlevel', 20))
            {
                $this->navigation->addNav('nav.borrow.label', 'bank.php?op=borrow');
            }
        }
        else
        {
            $this->navigation->addNav('nav.deposit.pay', 'bank.php?op=deposit');

            if ('' !== $this->settings->getSetting('borrowperlevel', 20) && '0' !== $this->settings->getSetting('borrowperlevel', 20))
            {
                $this->navigation->addNav('nav.borrow.more', 'bank.php?op=borrow');
            }
        }

        if (
            $this->settings->getSetting('allowgoldtransfer', 1)
            && ($session['user']['level'] >= $this->settings->getSetting('mintransferlev', 3) || $session['user']['dragonkills'] > 0)
        ) {
            $this->navigation->addNav('nav.transfer', 'bank.php?op=transfer');
        }

        return $this->{$method}($params, $request);
    }

    protected function enter(array $params): Response
    {
        return $this->renderBank($params);
    }

    protected function transfer(array $params): Response
    {
        global $session;

        $params['opt']              = 'transfer';
        $params['transferPerLevel'] = $this->settings->getSetting('transferperlevel', 25);
        $params['maxTransfer']      = $session['user']['level'] * $this->settings->getSetting('maxtransferout', 25);

        return $this->renderBank($params);
    }

    protected function transfer2(array $params, Request $request): Response
    {
        $to  = $request->request->getInt('to');
        $amt = abs($request->request->getInt('amount', 0));

        $params['opt']    = 'transfer2';
        $params['amount'] = $amt;
        $params['to']     = $to;

        /** @var Lotgd\Core\Repository\CharactersRepository $repository */
        $repository = $this->getDoctrine()->getRepository('LotgdCore:Avatar');
        $characters = $repository->findLikeName("%{$to}%", 100);

        $params['characters'] = $characters;

        return $this->renderBank($params);
    }

    protected function transfer3(array $params, Request $request): Response
    {
        global $session;

        $amt    = abs($request->request->getInt('amount'));
        $to     = $request->request->getInt('to');
        $maxout = $session['user']['level'] * $this->settings->getSetting('maxtransferout', 25);

        $params['opt']    = 'transfer3';
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
            $repository = $this->getDoctrine()->getRepository('LotgdCore:Avatar');
            $result     = $repository->find($to);

            $params['transferred'] = 0;
            if (null !== $result)
            {
                $maxtfer = $result->getLevel() * $this->settings->getSetting('transferperlevel', 25);

                if ($result->getTransferredtoday() >= $this->settings->getSetting('transferreceive', 3))
                {
                    $params['transferred'] = 'tomanytfer';
                    $params['name']        = $result->getName();
                }
                elseif ($maxtfer < $amt)
                {
                    $params['transferred'] = 'maxtfer';
                    $params['maxtfer']     = $maxtfer;
                    $params['name']        = $result->getName();
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

                    $this->getDoctrine()->getManager()->persist($result);
                    $this->getDoctrine()->getManager()->flush();

                    $this->log->debug("transferred {$amt} gold to", $result->getAcct()->getAcctid());

                    $subj = ['transfer3.success.mail.subject', [], $params['textDomain']];
                    $body = ['transfer3.success.mail.message', ['name' => $session['user']['name'], 'amount' => $amt], $params['textDomain']];

                    $this->systemMail->send($result->getAcct()->getAcctid(), $subj, $body);
                }
            }
        }

        return $this->renderBank($params);
    }

    protected function deposit(array $params): Response
    {
        $params['opt'] = 'deposit';

        return $this->renderBank($params);
    }

    protected function depositfinish(array $params, Request $request): Response
    {
        global $session;

        $amount = abs($request->request->getInt('amount'));
        $amount = (0 == $amount) ? $session['user']['gold'] : $amount;

        $params['amount']    = $amount;
        $params['deposited'] = false;
        $params['opt']       = 'depositend';

        if ($amount <= $session['user']['gold'])
        {
            $params['deposited'] = true;
            $this->log->debug('deposited '.$amount.' gold in the bank');
            $session['user']['goldinbank'] += $amount;
            $session['user']['gold'] -= $amount;
        }

        return $this->renderBank($params);
    }

    protected function borrow(array $params): Response
    {
        global $session;

        $maxborrow = $session['user']['level'] * $this->settings->getSetting('borrowperlevel', 20);

        $params['opt']       = 'borrow';
        $params['maxborrow'] = $maxborrow;

        return $this->renderBank($params);
    }

    protected function withdraw(array $params): Response
    {
        $params['opt'] = 'withdraw';

        return $this->renderBank($params);
    }

    protected function withdrawfinish(array $params, Request $request): Response
    {
        global $session;

        $amount = abs($request->request->getInt('amount'));
        $amount = (0 == $amount) ? $session['user']['goldinbank'] : $amount;

        $params['opt']        = 'withdrawend';
        $params['amount']     = $amount;
        $params['withdrawal'] = false;

        if ($amount > $session['user']['goldinbank'] && '' != $request->request->get('borrow'))
        {
            $lefttoborrow           = $amount;
            $maxborrow              = $session['user']['level'] * $this->settings->getSetting('borrowperlevel', 20);
            $params['withdrawal']   = 1;
            $params['lefttoborrow'] = $lefttoborrow;
            $params['maxborrow']    = $maxborrow;
            $params['borrowed']     = false;
            $params['didwithdraw']  = false;

            if ($lefttoborrow <= ($session['user']['goldinbank'] + $maxborrow))
            {
                $params['withdrawal'] = 2;

                if ($session['user']['goldinbank'] > 0)
                {
                    $params['goldInBank']  = $session['user']['goldinbank'];
                    $params['didwithdraw'] = true;
                    $lefttoborrow -= $session['user']['goldinbank'];
                    $session['user']['gold'] += $session['user']['goldinbank'];
                    $session['user']['goldinbank'] = 0;

                    $this->log->debug("withdrew {$amount} gold from the bank");
                }

                $params['lefttoborrow'] = $lefttoborrow;
                if (($lefttoborrow - $session['user']['goldinbank']) <= $maxborrow)
                {
                    $params['borrowed'] = true;
                    $session['user']['goldinbank'] -= $lefttoborrow;
                    $session['user']['gold'] += $lefttoborrow;

                    $this->log->debug("borrows {$lefttoborrow} gold from the bank");
                }
            }
        }
        elseif ($amount <= $session['user']['goldinbank'])
        {
            $params['withdrawal'] = true;
            $session['user']['goldinbank'] -= $amount;
            $session['user']['gold'] += $amount;

            $this->log->debug("withdrew {$amount} gold from the bank");
        }

        return $this->renderBank($params);
    }

    private function renderBank(array $params): Response
    {
        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_BANK_POST);
        $params = modulehook('page-bank-tpl-params', $args->getArguments());

        //-- Restore text domain for navigation
        $this->navigation->setTextDomain();

        return $this->render('page/bank.html.twig', $params);
    }
}
