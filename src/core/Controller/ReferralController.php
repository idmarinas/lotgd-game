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
use Lotgd\Core\Lib\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ReferralController extends AbstractController
{
    private $dispatcher;
    private $settings;

    public function __construct(EventDispatcherInterface $eventDispatcher, Settings $settings)
    {
        $this->dispatcher = $eventDispatcher;
        $this->settings   = $settings;
    }

    public function index(array $params, Request $request): Response
    {
        global $session;

        $url = $this->settings->getSetting('serverurl', sprintf('%s://%s', $request->getServer('REQUEST_SCHEME'), $request->getServer('HTTP_HOST')));

        if ( ! preg_match('/\\/$/', $url))
        {
            $url = $url.'/';
            $this->settings->saveSetting('serverurl', $url);
        }

        $params['serverUrl']     = $url;
        $params['refererAward']  = $this->settings->getSetting('refereraward', 25);
        $params['referMinLevel'] = $this->settings->getSetting('referminlevel', 4);

        /** @var Lotgd\Core\Repository\AccountsRepository */
        $repository = $this->getDoctrine()->getRepository('LotgdCore:Accounts');
        $query      = $repository->createQueryBuilder('u');

        $params['referrers'] = $query->select('u.refererawarded')
            ->addSelect('c.name', 'c.level', 'c.dragonkills')
            ->where('u.referer = :acct')
            ->leftJoin('LotgdCore:Characters', 'c', 'WITH', $query->expr()->eq('c.id', 'u.character'))
            ->setParameter('acct', $session['user']['acctid'])

            ->getQuery()
            ->getResult()
        ;

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_REFERRAL_POST);
        $params = modulehook('page-referral-tpl-params', $args->getArguments());

        return $this->render('page/referral.html.twig', $params);
    }
}
