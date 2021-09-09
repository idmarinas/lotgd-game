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

use Lotgd\Core\Event\Clan as EventClan;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\Sanitize;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClanController extends AbstractController
{
    private $dispatcher;
    private $log;
    private $translator;
    private $cache;
    private $sanitize;
    private $navigation;
    private $response;
    private $settings;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Log $log,
        TranslatorInterface $translator,
        TagAwareCacheInterface $cache,
        Sanitize $sanitize,
        Navigation $navigation,
        HttpResponse $response,
        Settings $settings
    ) {
        $this->dispatcher = $eventDispatcher;
        $this->log        = $log;
        $this->translator = $translator;
        $this->cache      = $cache;
        $this->sanitize   = $sanitize;
        $this->navigation = $navigation;
        $this->response   = $response;
        $this->settings   = $settings;
    }

    public function index(Request $request): Response
    {
        global $session, $claninfo;

        // Don't hook on to this text for your standard modules please, use "clan" instead.
        // This hook is specifically to allow modules that do other clans to create ambience.
        $args = new GenericEvent(null, ['textDomain' => 'page_clan', 'textDomainNavigation' => 'navigation_clan']);
        $this->dispatcher->dispatch($args, Events::PAGE_CLAN_PRE);
        $result               = modulehook('clan-text-domain', $args->getArguments());
        $textDomain           = $result['textDomain'];
        $textDomainNavigation = $result['textDomainNavigation'];
        unset($result);

        $costGold = (int) $this->settings->getSetting('goldtostartclan', 10000);
        $costGems = (int) $this->settings->getSetting('gemstostartclan', 15);

        $params = [
            'textDomain'           => $textDomain,
            'clanInfo'             => $claninfo,
            'clanOwnerName'        => $this->settings->getSetting('clanregistrar', '`%Karissa`0'),
            'costGold'             => $costGold,
            'costGems'             => $costGems,
            'includeTemplatesPre'  => [],
            'includeTemplatesPost' => [],
        ];

        //-- Change text domain for navigation
        $this->navigation->setTextDomain($textDomainNavigation);

        $this->navigation->addHeader('category.village');
        $this->navigation->villageNav();

        $this->navigation->addHeader('category.options');
        $this->navigation->addNav('nav.list.list', 'clan.php?op=list');

        $ranks = [
            CLAN_APPLICANT      => 'ranks.00',
            CLAN_MEMBER         => 'ranks.010',
            CLAN_OFFICER        => 'ranks.020',
            CLAN_ADMINISTRATIVE => 'ranks.025',
            CLAN_LEADER         => 'ranks.030',
            CLAN_FOUNDER        => 'ranks.031',
        ];

        $ranks = new EventClan(['ranks' => $ranks, 'textDomain' => $textDomain, 'clanid' => null]);
        $this->dispatcher->dispatch($ranks, EventClan::RANK_LIST);
        $ranks                = modulehook('clanranks', $ranks->getData());
        $params['ranksNames'] = $ranks['ranks'];

        $op     = (string) $request->query->get('op', '');
        $method = method_exists($this, $op) ? $op : 'enter';

        if (CLAN_APPLICANT == $session['user']['clanrank'] && 'apply' == $op)
        {
            $method = 'applicantApply';
        }
        elseif (CLAN_APPLICANT == $session['user']['clanrank'] && 'new' == $op)
        {
            $method = 'applicantNew';
        }
        elseif (CLAN_APPLICANT == $session['user']['clanrank'])
        {
            $method = 'applicant';
        }

        return $this->{$method}($params, $request);
    }

    public function enter(array $params)
    {
        global $session;

        $claninfo = $params['clanInfo'];

        $this->response->pageTitle('title.default', ['name' => $this->sanitize->fullSanitize($claninfo['clanname'])], $params['textDomain']);

        $this->navigation->addHeader('category.options');

        if ($session['user']['clanrank'] > CLAN_MEMBER)
        {
            $this->navigation->addNav('nav.default.update', 'clan.php?op=motd');
        }

        $this->navigation->addNav('nav.default.membership', 'clan.php?op=membership');
        $this->navigation->addNav('nav.default.online', 'list.php?op=clan');
        $this->navigation->addNav('nav.default.waiting.area', 'clan.php?op=waiting');
        $this->navigation->addNav('nav.default.withdraw', 'clan.php?op=withdraw', [
            'attributes' => [
                'data-options' => json_encode(['text' => $this->translator->trans('section.withdraw.confirm', [], $params['textDomain'])], JSON_THROW_ON_ERROR),
                'onclick'      => 'Lotgd.confirm(this, event)',
            ],
        ]);

        $claninfo      = $params['clanInfo'];
        $params['tpl'] = 'clan_default';

        /** @var Lotgd\Core\Repository\UserRepository $acctRepository */
        $acctRepository = $this->getDoctrine()->getRepository('LotgdCore:User');
        /** @var Lotgd\Core\Repository\CharactersRepository $charRepository */
        $charRepository = $this->getDoctrine()->getRepository('LotgdCore:Avatar');

        $result = $acctRepository->getClanAuthorNameOfMotdDescFromAcctId($claninfo['motdauthor'], $claninfo['descauthor']);

        $params['motdAuthorName'] = $result['motdauthname'] ?? '';
        $params['descAuthorName'] = $result['descauthname'] ?? '';
        unset($result);

        $params['leaders']         = $charRepository->getClanLeadersCount($claninfo['clanid']);
        $params['promotingLeader'] = false;

        if (0 == $params['leaders'])
        {
            //There's no leader here, probably because the leader's account expired.
            $result = $charRepository->getViableLeaderForClan($session['user']['clanid']);

            if ($result)
            {
                $charRepository->setNewClanLeader($result['id']);
                $params['newLeader'] = $result['name'];

                if ($result['acctid'] == $session['user']['acctid'])
                {
                    //if it's the current user, we'll need to update their
                    //session in order for the db write to take effect.
                    $session['user']['clanrank'] = CLAN_LEADER;
                }
                $params['promotingLeader'] = true;
            }
        }

        $params['membership'] = $charRepository->getClanMembershipDetails($claninfo['clanid']);

        return $this->renderClan($params);
    }

    public function detail(array $params, Request $request): Response
    {
        global $session;

        $params['tpl'] = 'clan_applicant_detail';

        $clanId = $request->query->getInt('clanid');

        /** @var Lotgd\Core\Repository\CharactersRepository $charRepository */
        $charRepository = $this->getDoctrine()->getRepository('LotgdCore:Avatar');
        /** @var Lotgd\Core\Repository\ClansRepository $clanRepository */
        $clanRepository = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Clans::class);

        $params['clanDetail']          = $clanRepository->find($clanId);
        $params['SU_AUDIT_MODERATION'] = $session['user']['superuser'] & SU_AUDIT_MODERATION;

        $this->response->pageTitle('title.detail', [
            'clanName'      => $this->sanitize->fullSanitize($params['clanDetail']->getClanname()),
            'clanShortName' => $params['clanDetail']->getClanshort(),
        ]);

        if (($session['user']['superuser'] & SU_AUDIT_MODERATION) && $request->isMethod('post'))
        {
            $clanName  = $this->sanitize->fullSanitize((string) $request->request->get('clanname'));
            $clanShort = $this->sanitize->fullSanitize((string) $request->request->get('clanshort'));

            $blockDesc   = $request->request->get('block');
            $unblockDesc = $request->request->get('unblock');

            if ($clanName && $clanShort)
            {
                $params['clanDetail']->setClanname($clanName)
                    ->setClanshort($clanShort)
                ;

                $this->addFlash('info', $this->translator->trans('section.detail.superuser.update.clan.names', [], $params['textDomain']));
            }

            if ($blockDesc)
            {
                $params['clanDetail']->setDescauthor(4294967295)
                    ->setClandesc($this->translator->trans('section.detail.superuser.update.clan.description.reason', [], $params['textDomain']))
                ;

                $this->addFlash('info', $this->translator->trans('section.detail.superuser.update.clan.description.block', [], $params['textDomain']));
            }
            elseif ($unblockDesc)
            {
                $params['clanDetail']->setDescauthor(0)
                    ->setClandesc('')
                ;

                $this->addFlash('info', $this->translator->trans('section.detail.superuser.update.clan.description.unblock', [], $params['textDomain']));
            }

            $this->getDoctrine()->getManager()->persist($params['clanDetail']);
            $this->getDoctrine()->getManager()->flush();
        }

        $params['membership'] = $charRepository->getClanMembershipList($clanId);
        $params['returnLink'] = $request->getServer('REQUEST_URI');

        return $this->renderClan($params);
    }

    public function list(array $params, Request $request): Response
    {
        $this->response->pageTitle('title.list', [], $params['textDomain']);

        $params['tpl'] = 'clan_applicant_list';

        $order = $request->query->getInt('order');

        /** @var Lotgd\Core\Repository\ClansRepository $clanRepository */
        $clanRepository = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Clans::class);

        $params['clanList'] = $clanRepository->getClanListWithMembersCount($order);

        $this->navigation->addHeader('category.options');

        if (\count($params['clanList']) > 0)
        {
            $this->navigation->addNav('nav.list.lobby', 'clan.php');

            $this->navigation->addHeader('category.sorting');
            $this->navigation->addNav('nav.list.order.count', 'clan.php?op=apply&order=0');
            $this->navigation->addNav('nav.list.order.name', 'clan.php?op=apply&order=1');
            $this->navigation->addNav('nav.list.order.short', 'clan.php?op=apply&order=2');
        }
        else
        {
            $this->navigation->addNav('nav.list.new', 'clan.php?op=new');
            $this->navigation->addNav('nav.list.lobby', 'clan.php');
        }

        return $this->renderClan($params);
    }

    public function waiting(array $params): Response
    {
        global $session;

        $params['tpl'] = 'clan_applicant_waiting';

        $this->response->pageTitle('title.applicant', [], $params['textDomain']);

        $this->navigation->addHeader('category.options');

        $nav = (CLAN_APPLICANT == $session['user']['clanrank']) ? 'lobby' : 'rooms';
        $this->navigation->addNav("nav.applicant.waiting.area.{$nav}", 'clan.php');

        return $this->renderClan($params);
    }

    public function applicantApply(array $params, Request $request): Response
    {
        $this->response->pageTitle('title.applicant', [], $params['textDomain']);

        $params['tpl'] = 'clan_applicant_apply';

        $clanId = $request->query->getInt('clanid');

        /** @var \Lotgd\Core\Repository\ClansRepository $clanRepository */
        $clanRepository = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Clans::class);

        if ($clanId > 0)
        {
            /** @var Lotgd\Core\Repository\CharactersRepository $charRepository */
            $charRepository = $this->getDoctrine()->getRepository('LotgdCore:Avatar');
            /** @var \Lotgd\Core\Repository\MailRepository $mailRepository */
            $mailRepository = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Mail::class);

            $this->addFlash('success', $this->translator->trans('flash.message.applicant.apply', [
                'clanOwnerName' => $this->sanitize->fullSanitize($params['clanOwnerName']),
            ], $params['textDomain']));

            $session['user']['clanid']       = $clanId;
            $session['user']['clanrank']     = CLAN_APPLICANT;
            $session['user']['clanjoindate'] = new \DateTime('now');

            $subj = ['mail.apply.subject', ['name' => $session['user']['name']], $params['textDomain']];
            $msg  = ['mail.apply.message', ['name' => $session['user']['name']], $params['textDomain']];

            $mailRepository->deleteMailFromSystemBySubj(serialize($subj));

            $leaders = $charRepository->getLeadersFromClan($session['user']['clanid'], $session['user']['acctid']);

            foreach ($leaders as $leader)
            {
                systemmail($leader['acctid'], $subj, $msg);
            }

            //-- Send reminder mail if clan of choice has a description
            $result = $clanRepository->find($clanId);

            if ('' != trim($result->getClandesc()))
            {
                $subj = ['mail.desc.reminder.subject', [], $params['textDomain']];
                $msg  = ['mail.desc.reminder.message', [
                    'clanName'      => $result->getClanname(),
                    'clanShortName' => $result->getClanshort(),
                    'description'   => $result->getClandesc(),
                ], $params['textDomain']];

                systemmail($session['user']['acctid'], $subj, $msg);
            }

            $this->navigation->addNavAllow('clan.php?op=waiting');

            return $this->redirect('clan.php?op=waiting');
        }

        $order = $request->query->getInt('order');

        $params['clanList'] = $clanRepository->getClanListWithMembersCount($order);

        if (($params['clanList'] !== []) > 0)
        {
            $this->navigation->addNav('nav.applicant.apply.lobby', 'clan.php');

            $this->navigation->addHeader('category.sorting');
            $this->navigation->addNav('nav.applicant.apply.order.count', 'clan.php?op=apply&order=0');
            $this->navigation->addNav('nav.applicant.apply.order.name', 'clan.php?op=apply&order=1');
        }
        else
        {
            $this->navigation->addNav('nav.applicant.apply.new', 'clan.php?op=new');
            $this->navigation->addNav('nav.applicant.apply.lobby', 'clan.php');
        }

        return $this->renderClan($params);
    }

    public function applicantNew(array $params, Request $request): Response
    {
        global $session;

        $this->response->pageTitle('title.applicant', [], $params['textDomain']);

        $this->navigation->addNav('nav.applicant.apply.lobby', 'clan.php');

        $params['tpl'] = 'clan_applicant_new';

        $params['clanShortNameLength'] = $this->settings->getSetting('clanshortnamelength', 5);

        $entity = new \Lotgd\Core\Entity\Clans();
        $form   = $this->createForm(\Lotgd\Core\Form\ClanNewType::class, $entity, [
            'action' => 'clan.php?op=new',
            'attr'   => [
                'autocomplete' => 'off',
            ],
        ]);

        $form->handleRequest($request);

        $params['clan_created'] = false;
        //-- Not have money
        if ($session['user']['gold'] < $params['costGold'] || $session['user']['gems'] < $params['costGems'])
        {
            $this->addFlash('warning', $this->translator->trans('section.applicant.new.form.validator.no.fees', $params, $params['textDomain']));
            $this->addFlash('error', $this->translator->trans('section.applicant.new.form.validator.denied', $params, $params['textDomain']));
        }
        elseif ($form->isSubmitted() && $form->isValid())
        {
            $args = new EventClan(['clanname' => $entity->getClanname(), 'clanshort' => $entity->getClanshort()]);
            $this->dispatcher->dispatch($args, EventClan::CREATE);
            $args = modulehook('process-createclan', $args->getData());

            if ($args['blocked'] ?? false)
            {
                $this->addFlash('warning', $args['blockmsg']);
            }
            else
            {
                $this->getDoctrine()->getManager()->persist($entity);
                $this->getDoctrine()->getManager()->flush();

                $params['clanName']  = $entity->getClanname();
                $params['clanShort'] = $entity->getClanshort();

                $session['user']['clanid']       = $entity->getClanid();
                $session['user']['clanrank']     = CLAN_FOUNDER;
                $session['user']['clanjoindate'] = new \DateTime('now');
                $session['user']['gold'] -= $params['costGold'];
                $session['user']['gems'] -= $params['costGems'];

                $this->addFlash('success', $this->translator->trans('section.applicant.new.form.success', $params, $params['textDomain']));

                $this->log->debug("has started a new clan (<{$entity->getClanshort()}> {$entity->getClanname()}) for {$params['costGold']} gold and {$params['costGems']} gems.");

                $params['clan_created'] = true;
            }
        }

        $params['form'] = $form->createView();

        return $this->renderClan($params);
    }

    public function applicant(array $params, Request $request): Response
    {
        global $session;

        $claninfo = $params['clanInfo'];

        $this->response->pageTitle('title.applicant', [], $params['textDomain']);

        $this->navigation->addHeader('category.options');

        if (($claninfo['clanid'] ?? 0) > 0)
        {
            //-- Applied for membership to a clan
            $this->navigation->addNav('nav.applicant.waiting.label', 'clan.php?op=waiting');
            $this->navigation->addNav('nav.applicant.withdraw', 'clan.php?op=withdraw');
        }
        else
        {
            //-- Hasn't applied for membership to any clan.
            $this->navigation->addNav('nav.applicant.apply.membership', 'clan.php?op=apply');
            $this->navigation->addNav('nav.applicant.apply.new', 'clan.php?op=new');
        }

        $params['tpl'] = 'clan_applicant';

        $this->dispatcher->dispatch(new EventClan(), EventClan::ENTER);
        modulehook('clan-enter');

        if ('withdraw' == $request->query->get('op'))
        {
            /** @var Lotgd\Core\Repository\MailRepository $mailRepository */
            $mailRepository = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Mail::class);

            $this->addFlash('info', $this->sanitize->fullSanitize($this->translator->trans('flash.message.applicant.withdraw', [
                'clanOwnerName' => $params['clanOwnerName'],
                'clanName'      => $claninfo['clanname'],
            ], $params['textDomain'])));

            $session['user']['clanid']       = 0;
            $session['user']['clanrank']     = CLAN_APPLICANT;
            $session['user']['clanjoindate'] = new \DateTime('0000-00-00 00:00:00');
            $claninfo                        = [];

            $subj = ['mail.apply.subject', ['name' => $session['user']['name']], $params['textDomain']];

            $mailRepository->deleteMailFromSystemBySubj(serialize($subj), $session['user']['acctid']);

            $subj = ['mail.desc.reminder.subject', [], $params['textDomain']];

            $mailRepository->deleteMailFromSystemBySubj(serialize($subj), $session['user']['acctid']);

            $this->cache->invalidateTags(["clan-user-{$session['user']['acctid']}"]);
        }

        return $this->renderClan($params);
    }

    public function motd(array $params, Request $request): Response
    {
        global $session;

        $this->response->pageTitle('title.motd', [], $params['textDomain']);

        $this->navigation->addHeader('category.options');
        $this->navigation->addNav('nav.motd.return', 'clan.php');

        $claninfo      = $params['clanInfo'];
        $params['tpl'] = 'clan_motd';

        if ($session['user']['clanrank'] < CLAN_OFFICER)
        {
            $this->addFlash('error', $this->translator->trans('secction.motd.messagess.error', [], $params['textDomain']));

            $this->navigation->addNavAllow('clan.php');

            return $this->redirect('clan.php');
        }

        /** @var Lotgd\Core\Repository\UserRepository $acctRepository */
        $acctRepository = $this->getDoctrine()->getRepository('LotgdCore:User');
        $clanRepository = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Clans::class);

        $result = $acctRepository->getClanAuthorNameOfMotdDescFromAcctId($claninfo['motdauthor'], $claninfo['descauthor']);

        $params['motdAuthorName'] = $result['motdauthname'] ?? '';
        $params['descAuthorName'] = $result['descauthname'] ?? '';
        unset($result);

        $clanmotd  = $this->sanitize->mbSanitize(mb_substr($request->request->get('clanmotd'), 0, 4096));
        $clandesc  = $this->sanitize->mbSanitize(mb_substr($request->request->get('clandesc'), 0, 4096));
        $customsay = $this->sanitize->mbSanitize(mb_substr($request->request->get('customsay'), 0, 15));

        $clanEntity = $clanRepository->find($claninfo['clanid']);

        $invalidateCache = false;

        if ($clanmotd && $claninfo['clanmotd'] != $clanmotd)
        {
            $invalidateCache = true;
            $clanEntity->setMotdauthor($session['user']['acctid'])
                ->setClanmotd($clanmotd)
            ;
            $claninfo['motdauthor'] = $session['user']['acctid'];
            $claninfo['clanmotd']   = $clanmotd;

            $this->addFlash('success', $this->translator->trans('section.motd.messagess.saved.motd', [], $params['textDomain']));
        }

        if ($clandesc && $claninfo['clandesc'] != $clandesc)
        {
            $invalidateCache = true;
            $clanEntity->setDescauthor($session['user']['acctid'])
                ->setClandesc($clandesc)
            ;
            $claninfo['descauthor'] = $session['user']['acctid'];
            $claninfo['clandesc']   = $clandesc;

            $this->addFlash('success', $this->translator->trans('section.motd.messagess.saved.desc', [], $params['textDomain']));
        }

        if ($customsay && $claninfo['customsay'] != $customsay)
        {
            $invalidateCache = true;
            $clanEntity->setCustomsay($customsay);
            $claninfo['customsay'] = $customsay;

            $this->addFlash('success', $this->translator->trans('section.motd.messagess.saved.say', [], $params['textDomain']));
        }

        if ($invalidateCache)
        {
            //-- Invalidate all cache items with tag "clan"
            $this->cache->invalidateTags(['clan']);
        }

        $this->getDoctrine()->getManager()->persist($clanEntity);
        $this->getDoctrine()->getManager()->flush();

        $params['clanInfo'] = $claninfo;

        return $this->renderClan($params);
    }

    public function membership(array $params, Request $request): Response
    {
        global $session, $claninfo;

        $this->response->pageTitle('title.membership', ['name' => $this->sanitize->fullSanitize($claninfo['clanname'])], $params['textDomain']);

        $this->navigation->addHeader('category.options');
        $this->navigation->addNav('nav.membership.hall', 'clan.php');

        $claninfo      = $params['clanInfo'];
        $params['tpl'] = 'clan_membership';

        /** @var Lotgd\Core\Repository\CharactersRepository $charRepository */
        $charRepository = $this->getDoctrine()->getRepository('LotgdCore:Avatar');

        $setrank   = $request->request->getInt('setrank');
        $whoacctid = $request->request->getInt('whoacctid');
        $remove    = $request->query->getInt('remove');

        if ($remove !== 0)
        {
            $character = $charRepository->getCharacterFromAcctidAndRank($remove, $session['user']['clanrank']);

            $args = new EventClan([
                'setrank' => 0,
                'login'   => $character->getAcct()->getLogin(),
                'name'    => $character->getName(),
                'acctid'  => $remove,
                'clanid'  => $session['user']['clanid'],
                'oldrank' => $character->getClanrank(),
            ]);

            $this->dispatcher->dispatch($args, EventClan::RANK_SET);
            $args = modulehook('clan-setrank', $args->getData());

            $character->setClanrank(CLAN_APPLICANT)
                ->setClanid(0)
                ->setClanjoindate(new \DateTime('0000-00-00 00:00:00'))
            ;

            $this->getDoctrine()->getManager()->persist($character);
            $this->getDoctrine()->getManager()->flush();

            $this->log->debug("Player {$session['user']['name']} removed player {$character->getAcct()->getLogin()} from {$claninfo['clanname']}.", $remove);

            //delete unread application emails from this user.
            //breaks if the applicant has had their name changed via
            //dragon kill, superuser edit, or lodge color change
            $subj = serialize(['mail.apply.subject', ['name' => $character->getName()], $params['textDomain']]);

            /** @var Lotgd\Core\Repository\MailRepository $mailRepository */
            $mailRepository = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Mail::class);
            $mailRepository->deleteMailFromSystemBySubj($subj);

            unset($character);
        }
        elseif ($setrank > 0 && $setrank <= $session['user']['clanrank'] && $whoacctid)
        {
            $character = $charRepository->findOneBy(['acct' => $whoacctid]);

            if ($character)
            {
                $args = new EventClan([
                    'setrank' => $setrank,
                    'login'   => $character->getAcct()->getLogin(),
                    'name'    => $character->getName(),
                    'acctid'  => $whoacctid,
                    'clanid'  => $session['user']['clanid'],
                    'oldrank' => $character->getClanrank(),
                ]);
                $this->dispatcher->dispatch($args, EventClan::RANK_SET);
                $args = modulehook('clan-setrank', $args->getData());

                if ( ! ($args['handled'] ?? false))
                {
                    $character->setClanrank(max(0, min($session['user']['clanrank'], $setrank)));

                    $this->log->debug("Player {$session['user']['name']} changed rank of {$character->getName()} to {$setrank}.", $whoacctid);

                    $this->getDoctrine()->getManager()->persist($character);
                    $this->getDoctrine()->getManager()->flush();

                    unset($character);
                }
            }
        }

        $params['validRanks'] = array_intersect_key($params['ranksNames'], range(0, $session['user']['clanrank']));
        $params['membership'] = $charRepository->getClanMembershipList($claninfo['clanid']);

        return $this->renderClan($params);
    }

    public function withdraw(array $params): Response
    {
        global $session;

        $params['tpl'] = 'clan_withdraw';

        $args = new EventClan([
            'clanid'   => $session['user']['clanid'],
            'clanrank' => $session['user']['clanrank'],
            'acctid'   => $session['user']['acctid'],
        ]);
        $this->dispatcher->dispatch($args, EventClan::WITHDRAW);
        modulehook('clan-withdraw', $args->getData());

        /** @var \Lotgd\Core\Repository\CharactersRepository $charRepository */
        $charRepository = $this->getDoctrine()->getRepository('LotgdCore:Avatar');

        if ($session['user']['clanrank'] >= CLAN_LEADER)
        {
            //-- Check if clan have more leaders
            $leadersCount = $charRepository->getClanLeadersCount($session['user']['clanid']);

            if (1 == $leadersCount || 0 == $leadersCount)
            {
                $result = $charRepository->getViableLeaderForClan($session['user']['clanid'], $session['user']['acctid']);

                if ($result)
                {
                    //-- there is no alternate leader, let's promote the
                    //-- highest ranking member (or oldest member in the
                    //-- event of a tie).  This will capture even people
                    //-- who applied for membership.
                    $charRepository->setNewClanLeader($result['id']);

                    $this->addFlash('info', $this->translator->trans('flash.message.withdraw.promoting.leader', [
                        'name' => $result['name'],
                        'sex'  => $result['sex'],
                    ], $params['textDomain']));
                }
                else
                {
                    $clanRepository = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Clans::class);
                    $clanEntity     = $clanRepository->find($session['user']['clanid']);

                    //-- There are no other members, we need to delete the clan.
                    $args = new EventClan(['clanid' => $session['user']['clanid'], 'clanEntity' => $clanEntity]);
                    $this->dispatcher->dispatch($args, EventClan::DELETE);
                    modulehook('clan-delete', $args->getData());

                    $this->getDoctrine()->getManager()->remove($clanEntity);
                    $this->getDoctrine()->getManager()->flush();

                    //just in case we goofed, we don't want to have to worry
                    //about people being associated with a deleted clan.
                    $charRepository->expelPlayersFromDeletedClan($session['user']['clanid']);

                    $this->addFlash('error', $this->translator->trans('flash.message.withdraw.deleting.clan', [], $params['textDomain']));

                    $this->log->game('Clan '.$session['user']['clanid'].' has been deleted, last member gone', 'Clan');

                    unset($clanEntity);
                }
            }
        }

        /** @var \Lotgd\Core\Repository\MailRepository $mailRepository */
        $mailRepository = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Mail::class);

        $subj = ['mail.withdraw.subject', ['name' => $session['user']['name']], $params['textDomain']];
        $msg  = ['mail.withdraw.message', ['name' => $session['user']['name']], $params['textDomain']];

        $mailRepository->deleteMailFromSystemBySubj(serialize($subj));

        $leaders = $charRepository->getLeadersFromClan($session['user']['clanid'], $session['user']['acctid']);

        foreach ($leaders as $leader)
        {
            systemmail($leader['acctid'], $subj, $msg);
        }

        $this->log->debug($session['user']['login'].' has withdrawn from his/her clan nº. '.$session['user']['clanid']);

        $session['user']['clanid']       = 0;
        $session['user']['clanrank']     = CLAN_APPLICANT;
        $session['user']['clanjoindate'] = new \DateTime('0000-00-00 00:00:00');

        $this->addFlash('info', $this->translator->trans('flash.message.withdraw.withdraw', [], $params['textDomain']));

        $this->navigation->addNavAllow('clan.php');

        return $this->redirect('clan.php');
    }

    private function renderClan(array $params): Response
    {
        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_CLAN_POST);
        $params = modulehook('page-clan-tpl-params', $args->getArguments());

        //-- Restore text domain for navigation
        $this->navigation->setTextDomain();

        return $this->render('page/clan.html.twig', $params);
    }
}
