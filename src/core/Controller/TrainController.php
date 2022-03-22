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

use Lotgd\Core\Combat\Battle;
use Lotgd\Core\Combat\Buffer;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Repository\MastersRepository;
use Lotgd\Core\Tool\CreatureFunction;
use Lotgd\Core\Tool\DateTime;
use Lotgd\Core\Tool\PlayerFunction;
use Lotgd\Core\Tool\SystemMail;
use Lotgd\Core\Tool\Tool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TrainController extends AbstractController
{
    protected $translationDomain;
    protected $translationDomainNavigation;

    private $dispatcher;
    private $navigation;
    private $response;
    private $serviceBattle;
    private $repository;
    private $settings;
    private $dateTime;
    private $translator;
    private $tool;
    private $log;
    private $buffer;
    private $playerFunction;
    private $creatureFunction;
    private $systemMail;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Navigation $navigation,
        HttpResponse $response,
        Battle $battle,
        MastersRepository $repository,
        Settings $settings,
        TranslatorInterface $translator,
        SystemMail $systemMail
    ) {
        $this->dispatcher    = $eventDispatcher;
        $this->navigation    = $navigation;
        $this->response      = $response;
        $this->serviceBattle = $battle;
        $this->repository    = $repository;
        $this->settings      = $settings;
        $this->translator    = $translator;
        $this->systemMail    = $systemMail;
    }

    public function index(Request $request): Response
    {
        global $session;

        // Don't hook on to this text for your standard modules please, use "train" instead.
        // This hook is specifically to allow modules that do other trains to create ambience.
        $args = new GenericEvent(null, ['textDomain' => 'page_train', 'textDomainNavigation' => 'navigation_train']);
        $this->dispatcher->dispatch($args, Events::PAGE_TRAIN_PRE);
        $result = modulehook('train-text-domain', $args->getArguments());

        $this->translationDomain           = $result['textDomain'];
        $this->translationDomainNavigation = $result['textDomainNavigation'];
        unset($result);

        $this->response->pageTitle('title', [], $this->translationDomain);

        //-- Change text domain for navigation
        $this->navigation->setTextDomain($this->translationDomainNavigation);

        $masterId = $request->query->getInt('master');
        $master   = $this->getMasterInfo($masterId);
        $masterId = $master['creatureid'];

        $params = [
            'textDomain'                    => $this->translationDomain,
            'translation_domain'            => $this->translationDomain,
            'translation_domain_navigation' => $this->translationDomainNavigation,
            'master_id'                     => $masterId,
            'master'                        => $master,
            'battle'                        => false,
        ];

        $params['masterName']  = $params['master']['creaturename'];
        $params['master_name'] = $params['master']['creaturename'];

        if (0 !== $masterId && $session['user']['level'] < (int) $this->settings->getSetting('maxlevel', 15))
        {
            return $this->training($params, $request);
        }

        return $this->enter($params);
    }

    public function training(array $params, Request $request): Response
    {
        global $session;

        $masterId = $params['master_id'];

        $level       = $session['user']['level'];
        $dks         = $session['user']['dragonkills'];
        $exprequired = $this->tool->expForNextLevel($level, $dks);

        $op = (string) $request->query->get('op', '');

        if ('' == $op)
        {
            $this->dateTime->checkDay();

            $params['tpl'] = 'default';

            $this->navigation->addHeader('category.navigation');
            $this->navigation->villageNav();

            $this->navigation->addHeader('category.actions');
            $this->navigation->addNav('nav.question', "train.php?op=question&master={$masterId}");
            $this->navigation->addNav('nav.challenge', "train.php?op=challenge&master={$masterId}");

            if (($session['user']['superuser'] & SU_DEVELOPER) !== 0)
            {
                $this->navigation->addNav('nav.superuser', "train.php?op=challenge&victory=1&master={$masterId}");
            }
        }
        elseif ('fight' == $op)
        {
            return $this->fight($params, $request);
        }
        elseif ('challenge' == $op)
        {
            return $this->challenge($params, $request);
        }
        elseif ('question' == $op)
        {
            $params['tpl'] = 'question';

            $this->dateTime->checkDay();

            $this->navigation->addHeader('category.navigation');
            $this->navigation->villageNav();

            $this->navigation->addHeader('category.actions');
            $this->navigation->addNav('nav.question', "train.php?op=question&master={$masterId}");
            $this->navigation->addNav('nav.challenge', "train.php?op=challenge&master={$masterId}");

            if (($session['user']['superuser'] & SU_DEVELOPER) !== 0)
            {
                $this->navigation->addNav('nav.superuser', "train.php?op=challenge&victory=1&master={$masterId}");
            }

            $params['expRequired'] = $exprequired;
            $params['expNeed']     = $exprequired - $session['user']['experience'];
        }
        elseif ('autochallenge' == $op)
        {
            return $this->autochallenge($params, $request);
        }

        return $this->renderTrain($params);
    }

    public function challenge(array $params, Request $request): Response
    {
        global $session;

        $level       = $session['user']['level'];
        $dks         = $session['user']['dragonkills'];
        $exprequired = $this->tool->expForNextLevel($level, $dks);

        $params['tpl'] = 'challenge';

        if ('' !== $request->query->getAlnum('victory') && '0' !== $request->query->getAlnum('victory'))
        {
            if ($session['user']['experience'] < $exprequired)
            {
                $session['user']['experience'] = $exprequired;
            }

            $session['user']['seenmaster'] = 0;
        }

        if ($session['user']['seenmaster'] && 0 == (int) $this->settings->getSetting('multimaster', 0))
        {
            $this->navigation->addHeader('category.navigation');
            $this->navigation->villageNav();
            $this->navigation->addHeader('category.actions');
        }
        else
        {
            /* OK, let's fix the multimaster thing */
            $session['user']['seenmaster'] = 1;
            $this->log->debug('Challenged master, setting seenmaster to 1');

            if ($session['user']['experience'] >= $exprequired)
            {
                $this->buffer->restoreBuffFields();

                $params['master'] = $this->creatureFunction->buffBadguy($params['master'], 'buffmaster');

                $attackstack['enemies'][0]      = $params['master'];
                $attackstack['options']['type'] = 'train';
                $session['user']['badguy']      = $attackstack;

                $params['battle'] = true;

                return $this->fight($params, $request);
            }
            else
            {
                $params['playerWeapon'] = $session['user']['weapon'];
                $params['playerArmor']  = $session['user']['armor'];

                $this->navigation->addHeader('category.navigation');
                $this->navigation->villageNav();
                $this->navigation->addHeader('category.actions');
            }
        }

        return $this->renderTrain($params);
    }

    public function autochallenge(array $params, Request $request): Response
    {
        global $session;

        $params['tpl'] = 'autochallenge';
        $masterId      = $params['master_id'];

        $this->navigation->addNav('nav.fight', "train.php?op=challenge&master={$masterId}");

        $params['playerHealed'] = false;

        if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
        {
            $params['playerHealed']       = true;
            $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
        }

        $this->dispatcher->dispatch(new GenericEvent(), Events::PAGE_TRAIN_AUTOCHALLENGE);
        modulehook('master-autochallenge');

        if ('' !== $this->settings->getSetting('displaymasternews', 1) && '0' !== $this->settings->getSetting('displaymasternews', 1))
        {
            $this->tool->addNews('news.autochallenge', [
                'playerName' => $session['user']['name'],
                'masterName' => $params['master']['creaturename'],
            ], $this->translationDomain);
        }

        return $this->renderTrain($params);
    }

    public function fight(array $params, Request $request): Response
    {
        global $companions, $session;

        $op = (string) $request->query->get('op');

        if ('fight' == $op)
        {
            $params['battle'] = true;
        }
        elseif ('run' == $op)
        {
            $this->addFlash('warning', $this->translator->trans('flash.message.fight.run', [], $this->translationDomain));

            $request->query->set('op', 'fight');
            $params['battle'] = true;
        }

        if ($params['battle'])
        {
            //-- Superuser Gain level
            if ($request->query->getAlnum('victory') && ($session['user']['superuser'] & SU_DEVELOPER))
            {
                $session['user']['badguy']['enemies'][0]['creaturehealth'] = 0;
            }

            //-- Battle zone.
            $this->serviceBattle->initialize(); //--* Initialize the battle
            $this->serviceBattle->suspendBuffs('allowintrain');
            $this->serviceBattle->suspendCompanions('allowintrain');
            $this->serviceBattle
                //-- Configuration
                ->setBattleZone('train') //-- Battle zone is "forest" by default.
                ->disableDie()
                ->disableCreateNews()
                ->disableProccessBatteResults()
                //-- Battle
                ->battleStart() //--* Start the battle.
                ->battleProcess() //--* Proccess the battle rounds.
                ->battleEnd() //--* End the battle for this petition
            ;

            $badguy = $this->serviceBattle->getEnemies()[0];

            if ($this->serviceBattle->isVictory())
            {
                ++$session['user']['level'];
                $session['user']['maxhitpoints'] += 10;
                $session['user']['soulpoints']   += 5;
                ++$session['user']['attack'];
                ++$session['user']['defense'];

                $session['user']['hitpoints'] = $session['user']['maxhitpoints'];

                // Fix the multimaster bug
                if (1 == $this->settings->getSetting('multimaster', 1))
                {
                    $session['user']['seenmaster'] = 0;
                    $this->log->debug('Defeated master, setting seenmaster to 0');
                }

                $this->serviceBattle->addContextToBattleEnd(['battle.end.victory.end', [], $this->translationDomain]);
                $this->serviceBattle->addContextToBattleEnd(['battle.end.victory.level', ['level' => $session['user']['level']], $this->translationDomain]);
                $this->serviceBattle->addContextToBattleEnd(['battle.end.victory.hitpoints', ['hitpoints' => $session['user']['maxhitpoints']], $this->translationDomain]);
                $this->serviceBattle->addContextToBattleEnd(['battle.end.victory.attack', [], $this->translationDomain]);
                $this->serviceBattle->addContextToBattleEnd(['battle.end.victory.defense', [], $this->translationDomain]);

                $this->serviceBattle->addContextToBattleEnd([
                    (($session['user']['level'] < 15) ? 'battle.end.victory.master.new' : 'battle.end.victory.master.none'),
                    [],
                    $this->translationDomain,
                ]);

                if ($session['user']['referer'] > 0 && ($session['user']['level'] >= $this->settings->getSetting('referminlevel', 4) || $session['user']['dragonkills'] > 0) && $session['user']['refererawarded'] < 1)
                {
                    $entity = $this->getDoctrine()->getRepository('LotgdCore:User')->find($session['user']['referer']);

                    if (null !== $entity)
                    {
                        $donation = $this->settings->getSetting('refereraward', 25);

                        $subject = ['mail.referer.subject', [], $this->translationDomain];
                        $message = ['mail.referer.message', [
                            'playerName'     => $session['user']['name'],
                            'level'          => $session['user']['level'],
                            'donationPoints' => $donation,
                        ], $this->translationDomain];

                        $entity->setDonation($entity->getDonation() + $donation);

                        $this->getDoctrine()->getManager()->persist($entity);
                        $this->getDoctrine()->getManager()->flush();

                        $this->systemMail->send($session['user']['referer'], $subject, $message);
                    }

                    $session['user']['refererawarded'] = 1;
                }

                $this->playerFunction->incrementSpecialty('`^');

                // Level-Up companions
                // We only get one level per pageload. So we just add the per-level-values.
                // No need to multiply and/or substract anything.
                if ('' !== $this->settings->getSetting('companionslevelup', 1) && '0' !== $this->settings->getSetting('companionslevelup', 1))
                {
                    $newcompanions = $companions;

                    foreach ($companions as $name => $companion)
                    {
                        $companion['attack']       += $companion['attackperlevel']             ?? 0;
                        $companion['defense']      += $companion['defenseperlevel']           ?? 0;
                        $companion['maxhitpoints'] += $companion['maxhitpointsperlevel'] ?? 0;
                        $companion['hitpoints'] = $companion['maxhitpoints'];
                        $newcompanions[$name]   = $companion;
                    }
                    $companions = $newcompanions;
                }

                if ('' !== $this->settings->getSetting('displaymasternews', 1) && '0' !== $this->settings->getSetting('displaymasternews', 1))
                {
                    $this->tool->addNews('news.victory', [
                        'sex'        => $session['user']['sex'],
                        'playerName' => $session['user']['name'],
                        'masterName' => $badguy['creaturename'],
                        'level'      => $session['user']['level'],
                        'age'        => $session['user']['age'],
                    ], $this->translationDomain);
                }

                $args = new GenericEvent(null, ['badguy' => $badguy, 'messages' => []]);
                $this->dispatcher->dispatch($args, Events::PAGE_TRAIN_TRANING_VICTORY);
                $result = modulehook('training-victory', $args->getArguments());

                array_walk($result['messages'], function ($elem)
                {
                    $this->serviceBattle->addContextToBattleEnd($elem);
                });

                $this->navigation->addHeader('category.navigation');
                $this->navigation->villageNav();

                $this->navigation->addHeader('category.actions');
                $this->navigation->addNav('nav.question', 'train.php?op=question');
                $this->navigation->addNav('nav.challenge', 'train.php?op=challenge');

                if (($session['user']['superuser'] & SU_DEVELOPER) !== 0)
                {
                    $this->navigation->addNav('nav.superuser', 'train.php?op=challenge&victory=1');
                }
            }
            elseif ($this->serviceBattle->isDefeat())
            {
                if ('' !== $this->settings->getSetting('displaymasternews', 1) && '0' !== $this->settings->getSetting('displaymasternews', 1))
                {
                    $this->tool->addNews('deathmessage', [
                        'deathmessage' => [
                            'deathmessage' => 'news.defeated',
                            'params'       => [
                                'playerName' => $session['user']['name'],
                                'masterName' => $badguy['creaturename'],
                            ],
                            'textDomain' => $this->translationDomain,
                        ],
                        'taunt' => $this->tool->selectTaunt(),
                    ], '');
                }

                $session['user']['hitpoints'] = $session['user']['maxhitpoints'];

                $this->serviceBattle->addContextToBattleEnd(['battle.end.defeat.end', ['masterName' => $badguy['creaturename']], $this->translationDomain]);

                $args   = new GenericEvent(null, ['badguy' => $badguy, 'messages' => []]);
                $result = modulehook('training-defeat', $args->getArguments());

                array_walk($result['messages'], function ($elem)
                {
                    $this->serviceBattle->addContextToBattleEnd($elem);
                });

                $this->navigation->addHeader('category.navigation');
                $this->navigation->villageNav();

                $this->navigation->addHeader('category.actions');
                $this->navigation->addNav('nav.question', "train.php?op=question&master={$params['master_id']}");
                $this->navigation->addNav('nav.challenge', "train.php?op=challenge&master={$params['master_id']}");

                if (($session['user']['superuser'] & SU_DEVELOPER) !== 0)
                {
                    $this->navigation->addNav('nav.superuser', "train.php?op=challenge&victory=1&master={$params['master_id']}");
                }
            }
            elseif ( ! $this->serviceBattle->battleHasWinner())
            {
                $this->serviceBattle->fightNav(false, false, "train.php?master={$params['master_id']}");
            }

            //-- Add results to response by default (use ->battleResults(true) if you want return results)
            $this->serviceBattle->battleResults();

            if ($this->serviceBattle->battleHasWinner())
            {
                $this->serviceBattle->unsuspendBuffs('allowintrain');
                $this->serviceBattle->unSuspendCompanions('allowintrain');
            }
        }

        return $this->renderTrain($params);
    }

    public function enter(array $params): Response
    {
        $params['tpl'] = 'maxlevel';

        $this->dateTime->checkDay();

        $this->navigation->addHeader('category.navigation');
        $this->navigation->villageNav();
        $this->navigation->addHeader('category.actions');

        return $this->renderTrain($params);
    }

    /**
     * @required
     */
    public function setDateTime(DateTime $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * @required
     */
    public function setTool(Tool $tool): self
    {
        $this->tool = $tool;

        return $this;
    }

    /**
     * @required
     */
    public function setLog(Log $log): self
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @required
     */
    public function setBuffer(Buffer $buffer): self
    {
        $this->buffer = $buffer;

        return $this;
    }

    /**
     * @required
     */
    public function setPlayerFunction(PlayerFunction $playerFunction): self
    {
        $this->playerFunction = $playerFunction;

        return $this;
    }

    /**
     * @required
     */
    public function setCreatureFunction(CreatureFunction $creatureFunction): self
    {
        $this->creatureFunction = $creatureFunction;

        return $this;
    }

    private function getMasterInfo(int $masterId): array
    {
        global $session;

        if (0 !== $masterId)
        {
            return $this->repository->findOneMasterById($masterId);
        }

        $query = $this->repository->createQueryBuilder('u');

        $master = $query
            ->where('u.creaturelevel = :level')
            ->orderBy('rand()')
        ;
        $query = $this->repository->createTranslatebleQuery($master);
        $query
            ->setMaxResults(1)
            ->setParameter('level', $session['user']['level'])
        ;

        return $query->getArrayResult()[0] ?? [];
    }

    private function renderTrain(array $params): Response
    {
        //-- Restore text domain for navigation
        $this->navigation->setTextDomain();

        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_TRAIN_POST);
        $params = modulehook('page-train-tpl-params', $args->getArguments());

        return $this->render('page/train.html.twig', $params);
    }
}
