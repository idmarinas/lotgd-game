<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.2.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Event\Clan;
use Lotgd\Core\Event\Core;
use Lotgd\Core\Event\Other;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\Sanitize;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BioController extends AbstractController
{
    public const TRANSLATION_DOMAIN = 'page_bio';

    private $dispatcher;
    private $translator;
    private $navigation;

    private $response;

    private $sanitize;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator,
        Navigation $navigation,
        HttpResponse $response,
        Sanitize $sanitize
    ) {
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
        $this->navigation = $navigation;
        $this->response   = $response;
        $this->sanitize   = $sanitize;
    }

    public function index(Request $request): Response
    {
        global $session;

        $ret  = (string) $request->query->get('ret');
        $char = (string) $request->query->get('char');

        $return = 'list.php';
        if ($ret !== '' && $ret !== '0')
        {
            $return = preg_replace('/[&?]c=[[:digit:]]+/', '', $ret);
            $return = trim($return, '/');
        }

        /** @var \Lotgd\Core\Repository\UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository('LotgdCore:User');

        //-- Legacy support
        if ( ! is_numeric($char))
        {
            $char = $repository->getAcctIdFromLogin($char);
        }
        $target     = $repository->getCharacterInfoFromAcctId((int) $char);
        $recentNews = $repository->getCharacterNewsFromAcctId((int) $char);

        if (empty($target))
        {
            $this->addFlash('warning', $this->translator->trans('deleted', [], self::TRANSLATION_DOMAIN));

            $this->navigation->addNavAllow($return);

            $this->redirect($return);
        }

        $ranks = [
            CLAN_APPLICANT      => 'ranks.00',
            CLAN_MEMBER         => 'ranks.010',
            CLAN_OFFICER        => 'ranks.020',
            CLAN_ADMINISTRATIVE => 'ranks.025',
            CLAN_LEADER         => 'ranks.030',
            CLAN_FOUNDER        => 'ranks.031',
        ];

        $ranks = new Clan(['ranks' => $ranks, 'textDomain' => 'page_clan', 'clanid' => $target['clanid']]);
        $this->dispatcher->dispatch($ranks, Clan::RANK_LIST);
        $ranks = $ranks->getData();

        $args = new Core(['' => $this->translator->trans('character.specialtyname', [], 'app_default')]);
        $this->dispatcher->dispatch($args, Core::SPECIALTY_NAMES);
        $specialties = $args->getData();

        $params = [
            'textDomain'   => self::TRANSLATION_DOMAIN,
            'character'    => $target,
            'recentNews'   => $recentNews,
            'ranks'        => $ranks['ranks'],
            'specialties'  => $specialties,
            'RACE_UNKNOWN' => RACE_UNKNOWN,
        ];

        //-- Init page
        $this->response->pageTitle('title', ['name' => $this->sanitize->fullSanitize($target['name'])], self::TRANSLATION_DOMAIN);

        $this->navigation->addHeader('common.category.return');

        if (($session['user']['superuser'] & SU_EDIT_USERS) !== 0)
        {
            $this->navigation->addHeader('common.superuser.category');
            $this->navigation->addNav('bio.nav.user', "user.php?op=edit&userid={$char}");
        }

        $this->navigation->addHeader('common.category.return');
        if ('' == $ret)
        {
            $this->navigation->addNav('bio.nav.list', $return);
        }
        elseif ('list.php' == $return)
        {
            $this->navigation->addNav('bio.nav.list', $return);
        }
        else
        {
            $this->navigation->addNav('bio.nav.whence', $return);
            $this->navigation->addNav('bio.nav.village', 'village.php');
        }
        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_BIO_POST);
        $params = $args->getArguments();

        $args = new Other($target);
        $this->dispatcher->dispatch($args, Other::BIO_END);

        return $this->render('page/bio.html.twig', $params);
    }
}
