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
use Lotgd\Core\Navigation\Navigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReferralController extends AbstractController
{
    public const TRANSLATION_DOMAIN = 'page_referral';

    private $dispatcher;
    private $settings;
    private $response;
    private $navigation;
    private $translator;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Settings $settings,
        HttpResponse $response,
        Navigation $navigation,
        TranslatorInterface $translator
    ) {
        $this->dispatcher = $eventDispatcher;
        $this->settings   = $settings;
        $this->response   = $response;
        $this->navigation = $navigation;
        $this->translator = $translator;
    }

    public function index(Request $request): Response
    {
        global $session;

        if ( ! $session['user']['loggedin'])
        {
            $referral = (string) $request->query->get('r');

            $this->addFlash('info', $this->translator->trans('flash.message.referral.create', ['referral' => $referral], self::TRANSLATION_DOMAIN));

            return $this->redirect('create.php?r='.rawurlencode($referral));
        }

        $this->response->pageTitle('title', [], self::TRANSLATION_DOMAIN);

        $params = [
            'textDomain' => self::TRANSLATION_DOMAIN,
        ];

        if (file_exists('public/lodge.php'))
        {
            $this->navigation->addNav('common.nav.lodge', 'lodge.php');
        }
        else
        {
            $this->navigation->villageNav();
        }

        $url = $this->settings->getSetting('serverurl', sprintf('%s://%s', $request->getServer('REQUEST_SCHEME'), $request->getServer('HTTP_HOST')));

        if ( ! preg_match('/\\/$/', $url))
        {
            $url .= '/';
            $this->settings->saveSetting('serverurl', $url);
        }

        $params['serverUrl']     = $url;
        $params['refererAward']  = $this->settings->getSetting('refereraward', 25);
        $params['referMinLevel'] = $this->settings->getSetting('referminlevel', 4);

        /** @var Lotgd\Core\Repository\UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository('LotgdCore:User');
        $query      = $repository->createQueryBuilder('u');

        $params['referrers'] = $query->select('u.refererawarded')
            ->addSelect('c.name', 'c.level', 'c.dragonkills')
            ->where('u.referer = :acct')
            ->leftJoin('LotgdCore:Avatar', 'c', 'WITH', $query->expr()->eq('c.id', 'u.avatar'))
            ->setParameter('acct', $session['user']['acctid'])

            ->getQuery()
            ->getResult()
        ;

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_REFERRAL_POST);
        $params = $args->getArguments();

        return $this->render('page/referral.html.twig', $params);
    }
}
