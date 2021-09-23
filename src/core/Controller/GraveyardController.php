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

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Combat\Battle;
use Lotgd\Core\Combat\Buffer;
use Lotgd\Core\Event\Graveyard;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\CreatureFunction;
use Lotgd\Core\Tool\Tool;
use Lotgd\CoreBundle\OccurrenceBundle\OccurrenceDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GraveyardController extends AbstractController
{
    protected $translationDomain;
    protected $translationDomainNavigation;

    private $dispatcher;
    private $response;
    private $settings;
    private $navigation;
    private $serviceBattle;
    private $buffer;
    private $serviceCreatureFunction;
    private $occurrenceDispatcher;
    private $tool;
    private $translator;
    private $doctrine;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        HttpResponse $response,
        Settings $settings,
        Navigation $navigation,
        Buffer $buffer,
        Tool $tool,
        TranslatorInterface $translator
    ) {
        $this->dispatcher = $dispatcher;
        $this->response   = $response;
        $this->settings   = $settings;
        $this->navigation = $navigation;
        $this->buffer     = $buffer;
        $this->tool       = $tool;
        $this->translator = $translator;
    }

    public function index(array $params, Request $request): Response
    {
        global $session;

        // Don't hook on to this text for your standard modules please, use "graveyard" instead.
        // This hook is specifically to allow modules that do other graveyards to create ambience.
        $args = new GenericEvent(null, ['textDomain' => 'page_graveyard', 'textDomainNavigation' => 'navigation_graveyard']);
        $this->dispatcher->dispatch($args, Events::PAGE_GRAVEYARD_PRE);
        $result                            = modulehook('graveyard-text-domain', $args->getArguments());
        $this->translationDomain           = $result['textDomain'];
        $this->translationDomainNavigation = $result['textDomainNavigation'];

        $params['textDomain']                    = $this->translationDomain;
        $params['translation_domain']            = $this->translationDomain;
        $params['translation_domain_navigation'] = $this->translationDomainNavigation;
        $params['graveyardOwnerName']            = $this->settings->getSetting('deathoverlord', '`$Ramius`0');
        $params['battle']                        = false;

        $this->response->pageTitle('title', [], $this->translationDomain);

        $this->navigation->setTextDomain($this->translationDomainNavigation);

        $this->navigation->addHeader('category.navigation');

        $this->buffer->stripAllBuffs();
        $max  = $session['user']['level'] * 10 + $session['user']['dragonkills'] * 2 + 50;
        $args = new GenericEvent(null, ['favor' => round(10 * ($max - $session['user']['soulpoints']) / $max)]);
        $this->dispatcher->dispatch($args, Events::PAGE_GRAVEYARD_HEAL);
        $favortoheal = modulehook('favortoheal', $args->getArguments());
        $favortoheal = (int) $favortoheal['favor'];

        $params['favorToHeal'] = $favortoheal;
        $params['max_heal']    = $max;

        $op     = (string) $request->query->get('op', '');
        $op     = 'run' == $op ? 'fight' : $op;
        $method = method_exists($this, $op) ? $op : 'default';

        return $this->{$method}($params, $request);
    }

    public function fight(array $params, Request $request): Response
    {
        global $session;

        $params['tpl'] = 'default';

        $battle = $params['battle'];

        $op = (string) $request->query->get('op', '');

        if ('run' == $op)
        {
            if (1 == mt_rand(0, 2))
            {
                $this->addFlash('success', $this->translator->trans('flash.message.battle.run.success', [
                    'graveyardOwnerName' => $params['graveyardOwnerName'],
                ], $params['translation_domain']));
                $favor = 5 + mt_rand(0, $session['user']['level']);
                $favor = min($favor, $session['user']['deathpower']);

                if ($favor > 0)
                {
                    $this->addFlash('error', $this->translator->trans('flash.message.battle.run.lost', [
                        'favor'              => $favor,
                        'graveyardOwnerName' => $params['graveyardOwnerName'],
                    ], $params['translation_domain']));
                    $session['user']['deathpower'] -= $favor;
                }

                $this->navigation->addNav('nav.return.graveyard', 'graveyard.php');
            }
            else
            {
                $this->addFlash('error', $this->translator->trans('flash.message.battle.run.fail', [], $params['translation_domain']));
                $battle = true;
            }
        }
        elseif ('fight' == $op)
        {
            $battle = true;
        }

        if ($battle)
        {
            $params['tpl']               = 'default';
            $params['showGraveyardDesc'] = false;

            $this->serviceBattle
                ->initialize()
                //-- Configuration
                ->setBattleZone('graveyard')
                ->enableGhost() //-- Player is dead so configure as Ghost
                ->disableGainGold()
                ->disableGainExp()
                ->disableDie()
                ->disableLostExp()
                ->disableLostGold()
                ->enableGainFavor()

                ->battleStart()
                ->battleProcess()
                ->battleEnd()
                ->battleResults()
            ;

            if ($this->serviceBattle->battleHasWinner())
            {
                $battle = false;
                $op     = '';
                $request->query->set('op', '');
                $skipgraveyardtext           = true;
                $params['showGraveyardDesc'] = ! $skipgraveyardtext;
            }
            else
            {
                $this->serviceBattle->fightNav(false, true, 'graveyard.php');
            }

            //-- Revert changes not need more battle end
            $this->serviceBattle->disableGhost(); //-- Remember call this if you enableGhost
        }

        $params['battle'] = $battle;

        return $this->renderGraveyard($params, $request);
    }

    public function search(array $params, Request $request): Response
    {
        global $session;

        if ($session['user']['gravefights'] <= 0)
        {
            $this->addFlash('error', $this->translator->trans('flash.message.no.torments', [], $this->translationDomain));
            $request->query->set('op', '');
        }
        else
        {
            $this->serviceBattle->suspendCompanions('allowinshades', true);

            /** New occurrence dispatcher for special events. */
            $event = $this->occurrenceDispatcher->dispatch('graveyard', null, [
                'translation_domain'            => $this->translationDomain,
                'translation_domain_navigation' => $this->translationDomainNavigation,
                'route'                         => 'graveyard.php',
                'navigation_method'             => 'graveyardNav',
            ]);

            if ($event->isPropagationStopped())
            {
                $this->response->pageEnd();
            }
            elseif ($event['skip_description'])
            {
                $skipgraveyardtext           = true;
                $params['showGraveyardDesc'] = ! $skipgraveyardtext;

                $request->query->set('op', '');
            }
            //-- Only execute when NOT occurrence is in progress.
            elseif (0 != module_events('graveyard', $this->settings->getSetting('gravechance', 0)))
            {
                if ($this->navigation->checkNavs())
                {
                    $this->response->pageEnd();
                }

                // If we're going back to the graveyard, make sure to reset
                // the special and the specialmisc
                $session['user']['specialinc']  = '';
                $session['user']['specialmisc'] = '';

                $skipgraveyardtext           = true;
                $params['showGraveyardDesc'] = ! $skipgraveyardtext;

                $request->query->set('op', '');
            }
            else
            {
                --$session['user']['gravefights'];
                $level            = (int) $session['user']['level'];
                $params['battle'] = true;
                $result           = $this->serviceCreatureFunction->lotgdSearchCreature(1, $level, $level, false, false);

                $badguy = $this->serviceCreatureFunction->lotgdTransformCreature($result[0]);
                $badguy['creaturehealth']    += 50;
                $badguy['creaturemaxhealth'] += 50;

                // Make graveyard creatures easier.
                $badguy['creatureattack']  *= .7;
                $badguy['creaturedefense'] *= .7;

                // Add enemy
                $attackstack['enemies'][0]      = $badguy;
                $attackstack['options']['type'] = 'graveyard';

                //no multifights currently, so this hook passes the badguy to modify
                $attackstack = new Graveyard($attackstack);
                $this->dispatcher->dispatch($attackstack, Graveyard::FIGHT_START);
                $attackstack = modulehook('graveyardfight-start', $attackstack->getData());

                $session['user']['badguy'] = $attackstack;
            }
        }

        $method = $params['battle'] ? 'fight' : 'default';

        return $this->{$method}($params, $request);
    }

    public function default(array $params, Request $request): Response
    {
        $params['tpl'] = 'default';

        if ( ! $params['battle'])
        {
            $this->navigation->graveyardNav($this->translationDomainNavigation);
        }

        return $this->renderGraveyard($params, $request);
    }

    public function enter(array $params, Request $request): Response
    {
        $params['tpl'] = 'enter';

        $this->navigation->addHeader('category.places');
        $this->navigation->addNav('nav.return.graveyard', 'graveyard.php');
        $this->navigation->addNav('nav.shades', 'shades.php');
        $this->navigation->addHeader('category.souls');
        $this->navigation->addNav('nav.question', 'graveyard.php?op=question', [
            'params' => [
                'graveyardOwnerName' => $params['graveyardOwnerName'],
            ],
        ]);
        $this->navigation->addNav('nav.restore', 'graveyard.php?op=restore', [
            'params' => [
                'favor' => $params['favorToHeal'],
            ],
        ]);

        return $this->renderGraveyard($params, $request);
    }

    public function restore(array $params, Request $request): Response
    {
        global $session;

        $params['tpl'] = 'restore';

        if ($session['user']['soulpoints'] < $params['max_heal'])
        {
            if ($session['user']['deathpower'] >= $params['favorToHeal'])
            {
                $params['restored'] = true;
                $session['user']['deathpower'] -= $params['favorToHeal'];
                $session['user']['soulpoints'] = $params['max_heal'];
            }
            else
            {
                $params['restored'] = false;
            }
        }

        $this->navigation->addHeader('category.places');
        $this->navigation->addNav('nav.return.graveyard', 'graveyard.php');
        $this->navigation->addNav('nav.shades', 'shades.php');

        $this->navigation->addHeader('category.souls');
        $this->navigation->addNav('nav.question', 'graveyard.php?op=question', [
            'params' => [
                'graveyardOwnerName' => $params['graveyardOwnerName'],
            ],
        ]);

        return $this->renderGraveyard($params, $request);
    }

    public function resurrection(array $params, Request $request): Response
    {
        $params['tpl'] = 'resurrection';

        $this->navigation->addNav('nav.continue', 'newday.php?resurrection=true');

        return $this->renderGraveyard($params, $request);
    }

    public function question(array $params, Request $request): Response
    {
        global $session;

        $params['tpl'] = 'question';

        $this->navigation->addHeader('category.places');
        $this->navigation->addNav('nav.return.graveyard', 'graveyard.php');
        $this->navigation->addNav('nav.shades', 'shades.php');

        $this->navigation->addHeader('category.souls');
        $this->navigation->addNav('nav.question', 'graveyard.php?op=question', [
            'params' => [
                'graveyardOwnerName' => $params['graveyardOwnerName'],
            ],
        ]);
        $this->navigation->addNav('nav.restore', 'graveyard.php?op=restore', [
            'params' => [
                'favor' => $params['favorToHeal'],
            ],
        ]);

        $hauntcost        = (int) $this->settings->getSetting('hauntcost', 25);
        $resurrectioncost = (int) $this->settings->getSetting('resurrectioncost', 100);

        $default_actions = new Graveyard([
            [
                'textDomain'           => $this->translationDomain, //-- For translator text
                'textDomainNavigation' => $this->translationDomainNavigation, //-- For translation navigation
                'link'                 => 'graveyard.php?op=resurrection', //-- Full link for action
                'linkText'             => 'nav.resurrection', //-- Translator key for link
                'favor'                => $resurrectioncost, //-- Cost
                'text'                 => '', //-- Translator key for output in body
                'highest'              => 'section.question.highest', //-- Translator key, text that represent highest possible buy
                'params'               => [
                    'graveyardOwnerName' => $params['graveyardOwnerName'],
                ],
            ],
            [
                'textDomain'           => $this->translationDomain, //-- For translator text
                'textDomainNavigation' => $this->translationDomainNavigation, //-- For translation navigation
                'link'                 => 'graveyard.php?op=haunt', //-- Full link for action
                'linkText'             => 'nav.haunt', //-- Translator key for link
                'favor'                => $hauntcost, //-- Cost
                'text'                 => '', //-- Translator key for output in body
                'highest'              => 'haunt.paragraph', //-- Translator key, text that represent highest possible buy
                'params'               => [
                    'graveyardOwnerName' => $params['graveyardOwnerName'],
                ],
            ],
        ]);

        //build navigation
        $this->dispatcher->dispatch($default_actions, Graveyard::DEATH_OVERLORD_ACTIONS);
        $actions = modulehook('deathoverlord_actions', $default_actions->getData());

        $favorCostList = [];

        foreach ($actions as $key => $value)
        {
            if ($value['favor'] > $session['user']['deathpower'])
            {
                if ( ! isset($value['hidden']) || ! $value['hidden'])
                {
                    unset($actions[$key]);

                    continue; //-- Strip hidden
                }

                $actions[$key]['link'] = ''; //-- Deactivate not buyable
            }

            $favorCostList[$key] = $value['favor']; //cost of favor
        }

        asort($favorCostList);
        end($favorCostList);

        $high = key($favorCostList);

        if ($high !== null)
        {
            $params['highest'] = [
                $actions[$high]['highest'], //-- Translator key
                $actions[$high]['params'], //-- Translator params
                $actions[$high]['textDomain'], //-- Translator domain
            ];
        }

        $this->navigation->addHeader('category.question.favor', [
            'params' => [
                'graveyardOwnerName' => $params['graveyardOwnerName'],
            ],
        ]);

        $params['texts'] = [];

        foreach ($actions as $value)
        {
            $this->navigation->addNav('nav.question.favor', $value['link'], [
                'params' => [
                    'favor' => $value['favor'],
                    'text'  => $this->translator->trans($value['linkText'], $value['params'], $value['textDomainNavigation']),
                ],
            ]);

            if ($value['text'] ?? '')
            {
                $params['texts'] = [
                    $value['text'],
                    $value['params'],
                    $value['textDomain'],
                ];
            }
        }

        $this->navigation->addHeader('category.other');

        $this->dispatcher->dispatch(new Graveyard(), Graveyard::DEATH_OVERLORD_FAVORS);
        modulehook('ramiusfavors');

        return $this->renderGraveyard($params, $request);
    }

    public function haunt(array $params, Request $request): Response
    {
        $params['tpl'] = 'haunt';

        $this->navigation->addHeader('category.places');
        $this->navigation->addNav('nav.graveyard', 'graveyard.php');
        $this->navigation->addNav('nav.shades', 'shades.php');
        $this->navigation->addNav('nav.return.mausoleum', 'graveyard.php?op=enter');

        return $this->renderGraveyard($params, $request);
    }

    public function haunt2(array $params, Request $request): Response
    {
        $params['tpl'] = 'haunt2';

        $this->navigation->addNav('nav.question', 'graveyard.php?op=question', [
            'params' => [
                'graveyardOwnerName' => $params['graveyardOwnerName'],
            ],
        ]);
        $this->navigation->addNav('nav.restore', 'graveyard.php?op=restore', [
            'params' => [
                'favor' => $params['favorToHeal'],
            ],
        ]);

        $this->navigation->addHeader('category.places');
        $this->navigation->addNav('nav.graveyard', 'graveyard.php');
        $this->navigation->addNav('nav.shades', 'shades.php');
        $this->navigation->addNav('nav.return.mausoleum', 'graveyard.php?op=enter');

        $name = (string) $request->request->get('name', '');

        /** @var \Lotgd\Core\Repository\AvatarRepository $repository */
        $repository           = $this->doctrine->getRepository('LotgdCore:Avatar');
        $params['characters'] = $repository->findLikeName("%{$name}%", 100);

        return $this->renderGraveyard($params, $request);
    }

    public function haunt3(array $params, Request $request): Response
    {
        global $session;

        $params['tpl'] = 'haunt3';

        $this->navigation->addNav('nav.question', 'graveyard.php?op=question', [
            'params' => [
                'graveyardOwnerName' => $params['graveyardOwnerName'],
            ],
        ]);
        $this->navigation->addNav('nav.restore', 'graveyard.php?op=restore', [
            'params' => [
                'favor' => $params['favorToHeal'],
            ],
        ]);

        $this->navigation->addHeader('category.places');
        $this->navigation->addNav('nav.graveyard', 'graveyard.php');
        $this->navigation->addNav('nav.shades', 'shades.php');
        $this->navigation->addNav('nav.return.mausoleum', 'graveyard.php?op=enter');

        $characterId = (int) $request->query->get('charid');

        $repository          = $this->doctrine->getRepository('LotgdCore:Avatar');
        $params['character'] = $repository->find($characterId);

        $params['haunted'] = false;
        if ($params['character'])
        {
            $params['haunted'] = 0;
            if ( ! $params['character']->getHauntedby())
            {
                $session['user']['deathpower'] -= 25;

                $roll1 = e_rand(0, $params['character']->getLevel());
                $roll2 = e_rand(0, $session['user']['level']);

                $params['haunted'] = 1;
                $news              = 'news.haunted.fail';
                if ($roll2 > $roll1)
                {
                    $news              = 'news.haunted.success';
                    $params['haunted'] = true;

                    $params['character']->setHauntedby($session['user']['name']);

                    $this->doctrine->persist($params['character']);
                    $this->doctrine->flush();

                    $subject = ['mail.haunted.subject', [], $this->translationDomain];
                    $message = ['mail.haunted.message', [
                        'playerName' => $session['user']['name'],
                    ], $this->translationDomain];

                    systemmail($params['character']->getAcct()->getAcctid(), $subject, $message);
                }

                $this->tool->addNews($news, [
                    'playerName'  => $session['user']['name'],
                    'hauntedName' => $params['character']->getName(),
                ], $this->translationDomain);
            }
        }

        return $this->renderGraveyard($params, $request);
    }

    public function setServiceCreatureFunction(CreatureFunction $service): self
    {
        $this->serviceCreatureFunction = $service;

        return $this;
    }

    /**
     * @required
     */
    public function setServiceBattle(Battle $battle): self
    {
        $this->serviceBattle = $battle;

        return $this;
    }

    /**
     * @required
     */
    public function setOccurrenceDispatcher(OccurrenceDispatcher $occurrence): self
    {
        $this->occurrenceDispatcher = $occurrence;

        return $this;
    }

    /**
     * @required
     */
    public function setDoctrine(EntityManagerInterface $em): self
    {
        $this->doctrine = $em;

        return $this;
    }

    private function renderGraveyard(array $params, Request $request): Response
    {
        $this->dispatcher->dispatch(new GenericEvent(), Events::PAGE_GRAVEYARD);
        modulehook('deathoverlord');

        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_GRAVEYARD_POST);
        $params = modulehook('page-graveyard-tpl-params', $args->getArguments());

        $this->navigation->setTextDomain();

        $request->attributes->set('params', $params);

        return $this->render('page/graveyard.html.twig', $params);
    }
}
