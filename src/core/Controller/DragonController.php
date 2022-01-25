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

use Laminas\Hydrator\ClassMethodsHydrator;
use Lotgd\Core\Entity\Avatar;
use DateTime;
use DateInterval;
use Lotgd\Core\Combat\Battle;
use Lotgd\Core\Combat\Buffer;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\CreatureFunction;
use Lotgd\Core\Tool\Tool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DragonController extends AbstractController
{
    protected $translationDomain;
    protected $translationDomainNavigation;

    private $dispatcher;
    private $translator;
    private $settings;
    private $buffer;
    private $creatureFunction;
    private $response;
    private $serviceBattle;
    private $navigation;
    private $tool;
    private $log;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator,
        Settings $settings,
        Buffer $buffer,
        CreatureFunction $creatureFunction,
        HttpResponse $response,
        Navigation $navigation
    ) {
        $this->dispatcher       = $dispatcher;
        $this->translator       = $translator;
        $this->settings         = $settings;
        $this->buffer           = $buffer;
        $this->creatureFunction = $creatureFunction;
        $this->response         = $response;
        $this->navigation       = $navigation;
    }

    public function index(Request $request): Response
    {
        // Don't hook on to this text for your standard modules please, use "dragon" instead.
        // This hook is specifically to allow modules that do other dragons to create ambience.
        $args = new GenericEvent(null, ['textDomain' => 'page_dragon', 'textDomainNavigation' => 'navigation_app']);
        $this->dispatcher->dispatch($args, Events::PAGE_DRAGON_PRE);
        $result                            = modulehook('dragon-text-domain', $args->getArguments());
        $this->translationDomain           = $result['textDomain'];
        $this->translationDomainNavigation = $result['textDomainNavigation'];
        unset($result);

        $params = [
            'textDomain'                    => $this->translationDomain,
            'translation_domain'            => $this->translationDomain,
            'translation_domain_navigation' => $this->translationDomainNavigation,
            'battle'                        => false,
        ];

        $this->response->pageTitle('title', [], $this->translationDomain);

        $op     = (string) $request->query->get('op');
        $method = method_exists($this, $op) ? $op : 'enter';

        if ('run' == $method)
        {
            $this->addFlash('error', $this->translator->trans('battle.combat.run', [], $this->translationDomain));

            $method = 'fight';

            $params['battle'] = true;

            $request->query->set('op', 'fight');
        }
        elseif ('fight' == $method)
        {
            $params['battle'] = true;
        }

        return $this->{$method}($params, $request);
    }

    public function enter(array $params, Request $request): Response
    {
        global $session;

        if ( ! $request->query->get('nointro', ''))
        {
            $this->addFlash('warning', $this->translator->trans('intro', [], $this->translationDomain));
        }

        $maxlevel = $this->settings->getSetting('maxlevel', 15);
        $badguy   = [
            'creaturename'    => $this->translator->trans('creature.name', [], $this->translationDomain),
            'creaturelevel'   => $maxlevel + 2,
            'creatureweapon'  => $this->translator->trans('creature.weapon', [], $this->translationDomain),
            'creatureattack'  => 30 + $maxlevel,
            'creaturedefense' => 10 + $maxlevel,
            'creaturehealth'  => 150 + $maxlevel * 10,
            'creaturespeed'   => 2.5 + $maxlevel,
            'diddamage'       => 0,
            'type'            => 'dragon',
        ];

        //--  Transform Dragon to adapt to player
        $this->buffer->restoreBuffFields();
        $badguy = $this->creatureFunction->lotgdTransformCreature($badguy);
        $this->buffer->calculateBuffFields();

        $args = new GenericEvent(null, $badguy);
        $this->dispatcher->dispatch($args, Events::PAGE_DRAGON_BUFF);
        $badguy = modulehook('buffdragon', $args->getArguments());

        $session['user']['badguy'] = [
            'enemies' => [$badguy],
            'options' => [
                'type' => 'dragon',
            ],
        ];

        $params['battle'] = true;

        return $this->fight($params, $request);
    }

    public function prologue(array $params, Request $request)
    {
        global $session, $companions;

        $flawless = $request->query->getInt('flawless');

        $params['flawless'] = $flawless;

        $this->buffer->stripAllBuffs();
        $hydrator        = new ClassMethodsHydrator();
        $characterEntity = $hydrator->extract(new Avatar());
        $dkpoints        = 0;

        $this->buffer->restoreBuffFields();
        $args = new GenericEvent(null, [
            'total'    => $session['user']['maxhitpoints'],
            'dkpoints' => $dkpoints,
            'extra'    => $session['user']['maxhitpoints'] - $dkpoints - ($session['user']['level'] * 10),
            'base'     => $dkpoints + ($session['user']['level'] * 10),
        ]);
        $this->dispatcher->dispatch($args, Events::PAGE_DRAGON_HP_RECALC);
        $hpgain = modulehook('hprecalc', $args->getArguments());

        $this->buffer->calculateBuffFields();

        //-- Values that do not change when defeating the Dragon
        $nochange = [
            //-- Basic info
            'dragonkills'  => 1,
            'name'         => 1,
            'playername'   => 1,
            'sex'          => 1,
            'title'        => 1,
            'ctitle'       => 1,
            'bio'          => 1,
            'charm'        => 1,
            'dragonpoints' => 1,
            'gems'         => 1,
            'hashorse'     => 1,

            //-- Clan info
            'clanid'       => 1,
            'clanrank'     => 1,
            'clanjoindate' => 1,

            //-- Attributes
            'strength'     => 1,
            'dexterity'    => 1,
            'intelligence' => 1,
            'constitution' => 1,
            'wisdom'       => 1,

            //-- Other info
            'marriedto'     => 1,
            'lastmotd'      => 1,
            'bestdragonage' => 1,
            'dragonage'     => 1,
        ];

        $args = new GenericEvent(null, $nochange);
        $this->dispatcher->dispatch($args, Events::PAGE_DRAGON_DK_PRESERVE);
        $nochange = modulehook('dk-preserve', $args->getArguments());
        ++$session['user']['dragonkills'];

        $badguys = $session['user']['badguy']; //needed for the dragons name later

        $session['user']['dragonage'] = $session['user']['age'];

        if ($session['user']['dragonage'] < $session['user']['bestdragonage'] || 0 == $session['user']['bestdragonage'])
        {
            $session['user']['bestdragonage'] = $session['user']['dragonage'];
        }

        foreach ($characterEntity as $field => $value)
        {
            if ('id' == $field || 'acct' == $field || ($nochange[$field] ?? 0))
            {
                continue;
            }

            $session['user'][$field] = $value;
        }

        //-- Changed to custom default values for this
        $session['user']['location'] = $this->settings->getSetting('villagename', LOCATION_FIELDS);
        $session['user']['armor']    = $this->settings->getSetting('startarmor', 'T-Shirt');
        $session['user']['weapon']   = $this->settings->getSetting('startweapon', 'Fists');

        $newtitle = $this->tool->getDkTitle($session['user']['dragonkills'], $session['user']['sex']);

        $restartgold = $session['user']['gold'] + $this->settings->getSetting('newplayerstartgold', 50) * $session['user']['dragonkills'];
        $restartgems = 0;

        if ($restartgold > $this->settings->getSetting('maxrestartgold', 300))
        {
            $restartgold = $this->settings->getSetting('maxrestartgold', 300);
            $restartgems = max(0, ($session['user']['dragonkills'] - ($this->settings->getSetting('maxrestartgold', 300) / $this->settings->getSetting('newplayerstartgold', 50)) - 1));
            $restartgems = min($restartgems, $this->settings->getSetting('maxrestartgems', 10));
        }
        $session['user']['gold'] = $restartgold;
        $session['user']['gems'] += $restartgems;

        if (0 !== $flawless)
        {
            $session['user']['gold'] += 3 * $this->settings->getSetting('newplayerstartgold', 50);
            ++$session['user']['gems'];
        }

        $session['user']['maxhitpoints'] = 10 + $hpgain['dkpoints'] + $hpgain['extra'];
        $session['user']['hitpoints']    = $session['user']['maxhitpoints'];

        // Set the new title.
        $newname = $this->tool->changePlayerTitle($newtitle);

        $session['user']['title'] = $newtitle;
        $session['user']['name']  = $newname;

        $session['user']['laston'] = new DateTime('now');
        $session['user']['laston']->sub(new DateInterval('P1D')); //-- remove 1 day
        $session['user']['slaydragon'] = 1;
        $companions                    = [];
        $session['user']['companions'] = [];
        $session['user']['charm'] += 5;

        $regname = $this->tool->getPlayerBasename();

        foreach ($badguys['enemies'] as $opponent)
        {
            if ('dragon' == $opponent['type'])
            {
                $badguy = $opponent;

                break;
            }
        }

        $params['creatureName'] = $badguy['creaturename'];

        $this->tool->addNews('battle.victory.news.title', [
            'playerName'   => $regname,
            'title'        => $session['user']['title'],
            'times'        => $session['user']['dragonkills'],
            'creatureName' => $badguy['creaturename'],
        ], $this->translationDomain);

        $this->log->debug("slew the dragon and starts with {$session['user']['gold']} gold and {$session['user']['gems']} gems");

        // Moved this hear to make some things easier.
        $args = new GenericEvent();
        $this->dispatcher->dispatch($args, Events::PAGE_DRAGON_KILL);
        modulehook('dragonkill', $args->getArguments());

        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_DRAGON_POST);
        $params = modulehook('page-dragon-tpl-params', $args->getArguments());

        $this->navigation->addNav('common.nav.newday', 'news.php');

        return $this->render('page/dragon.html.twig', $params);
    }

    public function fight(array $params, Request $request): Response
    {
        global $session;

        $content = '';

        if ($params['battle'])
        {
            $this->serviceBattle->initialize();

            $this->serviceBattle
                ->setBattleZone('dragon')
                ->battleStart() //--* Start the battle.
                ->battleProcess() //--* Proccess the battle rounds.
                ->battleEnd() //--* End the battle for this petition
            ;

            foreach ($session['user']['badguy']['enemies'] as $opponent)
            {
                if ('dragon' == $opponent['type'])
                {
                    $badguy = $opponent;

                    break;
                }
            }

            if ($this->serviceBattle->isVictory())
            {
                $flawless = ! (bool) $badguy['diddamage'];

                $this->navigation->addNav('common.nav.continue', "dragon.php?op=prologue&flawless={$flawless}");

                $this->serviceBattle->addContextToBattleEnd([
                    'battle.end.victory.blow',
                    [
                        'creatureName' => $badguy['creaturename'],
                    ],
                    $this->translationDomain,
                ]);

                $this->tool->addNews('battle.victory.news.slain', [
                    'playerName'   => $session['user']['name'],
                    'creatureName' => $badguy['creaturename'],
                ], $this->translationDomain);
            }
            elseif ($this->serviceBattle->isDefeat())
            {
                $args = new GenericEvent();
                $this->dispatcher->dispatch($args, Events::PAGE_DRAGON_DEATH);
                $result = modulehook('dragondeath', $args->getArguments());

                foreach ($result as $msg)
                {
                    $this->serviceBattle->addContextToBattleEnd($msg);
                }
            }
            elseif ( ! $this->serviceBattle->battleHasWinner())
            {
                $this->serviceBattle->fightNav(true, false, 'dragon.php');
            }

            $content = $this->serviceBattle->battleResults(true);
        }

        return new Response($content);
    }

    /**
     * @required
     */
    public function setServiceBattle(Battle $serviceBattle): self
    {
        $this->serviceBattle = $serviceBattle;

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
}
