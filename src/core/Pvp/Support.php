<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Pvp;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Combat\Battle;
use Lotgd\Core\Event\Character;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Output\Format;
use Lotgd\Core\Tool\SystemMail;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Support
{
    public const TRANSLATION_DOMAIN = 'page_pvp';

    private $dispatcher;
    private $doctrine;
    private $settings;
    private $log;
    private $format;
    private $navigation;
    private $pvpWarning;
    private $battle;
    private $systemMail;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $doctrine,
        Settings $settings,
        Log $log,
        Format $format,
        Navigation $navigation,
        Warning $warning,
        Battle $battle,
        SystemMail $systemMail
    ) {
        $this->dispatcher = $dispatcher;
        $this->doctrine   = $doctrine;
        $this->settings   = $settings;
        $this->log        = $log;
        $this->format     = $format;
        $this->navigation = $navigation;
        $this->pvpWarning = $warning;
        $this->battle     = $battle;
        $this->systemMail = $systemMail;
    }

    public function setupPvpTarget(int $characterId)
    {
        global $session;

        $pvptime    = $this->settings->getSetting('pvptimeout', 600);
        $pvptimeout = new DateTime(date('Y-m-d H:i:s', strtotime("-{$pvptime} seconds")));

        /** @var \Lotgd\Core\Repository\AvatarRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:Avatar');
        $entity     = $repository->getCharacterForPvp($characterId);

        $message = 'flash.message.pvp.start.not.found';

        if ($entity)
        {
            $message = 'flash.message.pvp.start.tired';

            $pvprange = (int) $this->settings->getSetting('pvprange', 2);

            if (abs((int) $session['user']['level'] - (int) $entity['creaturelevel']) > $pvprange)
            {
                $message = 'flash.message.pvp.start.out.range';
            }
            elseif ($entity['pvpflag'] > $pvptimeout)
            {
                $message = 'flash.message.pvp.start.timeout';
            }
            elseif ($entity['loggedin'])
            {
                $message = 'flash.message.pvp.start.online';
            }
            elseif (1 != (int) $entity['alive'])
            {
                $message = 'flash.message.pvp.start.death';
            }
            elseif ($session['user']['playerfights'] > 0)
            {
                $entityCharacter = $repository->find($characterId);
                $entityCharacter->setPvpflag(new DateTime('now'));

                $this->doctrine->persist($entityCharacter);
                $this->doctrine->flush();

                $entity['creatureexp']    = round($entity['creatureexp'], 0);
                $entity['playerstarthp']  = $session['user']['hitpoints'];
                $entity['fightstartdate'] = new DateTime('now');

                $args = new Character(['entity' => $entity]);
                $this->dispatcher->dispatch($args, Character::PVP_ADJUST);
                $entity = modulehook('pvpadjust', $args->getData()['entity']);

                $this->pvpWarning->warning(true);

                return $entity;
            }
        }

        return $message;
    }

    public function pvpVictory($badguy, $killedloc)
    {
        global $session;

        // If the victim has logged on and banked some, give the lessor of the gold amounts.
        $repository = $this->doctrine->getRepository('LotgdCore:Avatar');
        $character  = $repository->find($badguy['character_id']);

        $badguy['creaturegold'] = ($character->getGold() > (int) $badguy['creaturegold'] ? (int) $badguy['creaturegold'] : $character->getGold());

        if (15 == $session['user']['level'])
        {
            $this->battle->addContextToBattleEnd([
                'battle.max.level',
                [],
                self::TRANSLATION_DOMAIN,
            ]);
        }

        // Winner of fight gets altered amount of gold based on badguy's level
        // and amount of gold they were carrying this can some times work to
        // their advantage, sometimes against.  The basic idea is to prevent
        // exhorbitant amounts of money from being transferred this way.
        $winamount = round(10 * $badguy['creaturelevel'] * log(max(1, $badguy['creaturegold'])), 0);

        if (15 == $session['user']['level'])
        {
            $winamount = 0;
        }
        $session['user']['gold'] += $winamount;

        $this->battle->addContextToBattleEnd([
            'battle.victory.creature',
            ['creatureName' => $badguy['creaturename']],
            self::TRANSLATION_DOMAIN,
        ]);
        $this->battle->addContextToBattleEnd([
            'battle.victory.gold',
            ['gold' => $winamount],
            self::TRANSLATION_DOMAIN,
        ]);

        $exp = round($this->settings->getSetting('pvpattgain', 10) * $badguy['creatureexp'] / 100, 0);

        if ('' !== $this->settings->getSetting('pvphardlimit', 0) && '0' !== $this->settings->getSetting('pvphardlimit', 0))
        {
            $exp = min($this->settings->getSetting('pvphardlimitamount', 15000), $exp);
        }

        if (15 == $session['user']['level'])
        {
            $exp = 0;
        }

        $expbonus = round(($exp * (1 + .1 * ($badguy['creaturelevel'] - $session['user']['level']))) - $exp, 0);

        if ($expbonus > 0)
        {
            $this->battle->addContextToBattleEnd([
                'battle.victory.difficult',
                ['experience' => $expbonus],
                self::TRANSLATION_DOMAIN,
            ]);
        }
        elseif ($expbonus < 0)
        {
            $this->battle->addContextToBattleEnd([
                'battle.victory.simplistic',
                ['experience' => abs($expbonus)],
                self::TRANSLATION_DOMAIN,
            ]);
        }
        $wonexp = $exp + $expbonus;
        $this->battle->addContextToBattleEnd([
            'battle.victory.experience',
            ['experience' => $wonexp],
            self::TRANSLATION_DOMAIN,
        ]);
        $session['user']['experience'] += $wonexp;

        $lostexp = round($badguy['creatureexp'] * $this->settings->getSetting('pvpdeflose', 5) / 100, 0);

        //player wins gold and exp from badguy
        $this->log->debug("started the fight and defeated {$badguy['creaturename']} in {$killedloc} (earned {$winamount} of {$badguy['creaturegold']} gold and {$wonexp} of {$lostexp} exp)", false, $session['user']['acctid']);
        $this->log->debug("was victim and has been defeated by {$session['user']['name']} in {$killedloc} (lost {$badguy['creaturegold']} gold and {$lostexp} exp, actor tooks {$winamount} gold and {$wonexp} exp)", false, $badguy['acctid']);

        $args = new Character(['pvpmessageadd' => '', 'handled' => false, 'badguy' => $badguy]);
        $this->dispatcher->dispatch($args, Character::PVP_WIN);
        $args = modulehook('pvpwin', $args->getData());

        $subject = ['mail.victory.subject', ['location' => $killedloc], self::TRANSLATION_DOMAIN];

        $message = [
            'mail.victory.message',
            [
                'location'        => $killedloc,
                'playerName'      => $session['user']['name'],
                'playerWeapon'    => $session['user']['weapon'],
                'creatureStartHp' => $badguy['playerstarthp'],
                'playerHitpoints' => $session['user']['hitpoints'],
                'expLose'         => $lostexp,
                'goldLost'        => $badguy['creaturegold'],
                'pvpDefLose'      => ((int) $this->settings->getSetting('pvpdeflose', 5) / 100),
                'pvpMessageAdded' => $args['pvpmessageadd'],
                'date'            => $badguy['fightstartdate'],
                'dateRelative'    => $this->format->relativedate($badguy['fightstartdate']),
                //-- Subjective pronoun for the player (him her)
                'himHer' => $session['user']['sex'] ? 'her' : 'him',
                //-- Possessive pronoun for the player (his her)
                'hisHer' => $session['user']['sex'] ? 'her' : 'his',
                //-- Objective pronoun for the player (he she)
                'heShe' => $session['user']['sex'] ? 'she' : 'he',
            ],
            self::TRANSLATION_DOMAIN,
        ];

        // /\- Gunnar Kreitz
        $this->systemMail->send($badguy['acctid'], $subject, $message);
        // /\- Gunnar Kreitz

        $character->setAlive(false);

        $goldLost = $character->getGold() - $badguy['creaturegold'];
        $character->setGold($goldLost);

        if ($goldLost < 0)
        {
            $character->setGold(0);
            //-- Avoid debs in bank of looser
            $character->setGoldinbank(max($character->getGoldinbank() - abs($goldLost), 0));
        }

        $character->setExperience(max($character->getExperience() - $lostexp, 0));

        $this->doctrine->persist($character);
        $this->doctrine->flush();

        return $args['handled'];
    }

    public function pvpDefeat($badguy, $killedloc)
    {
        global $session;

        $this->navigation->addNav('battle.nav.news', 'news.php', ['textDomain' => 'navigation_app']);

        $badguy['acctid']       = (int) $badguy['acctid'];
        $badguy['creaturegold'] = (int) $badguy['creaturegold'];

        // Winner of fight gets altered amount of gold based on badguy's level
        // and amount of gold they were carrying this can some times work to
        // their advantage, sometimes against.  The basic idea is to prevent
        // exhorbitant amounts of money from being transferred this way.
        $winamount = round(10 * $session['user']['level'] * log(max(1, $session['user']['gold'])), 0);

        if (15 == $badguy['creaturelevel'])
        {
            $winamount = 0;
        }

        $repository = $this->doctrine->getRepository('LotgdCore:Avatar');
        $character  = $repository->find($badguy['character_id']);

        $wonexp = round($session['user']['experience'] * $this->settings->getSetting('pvpdefgain', 10) / 100, 0);

        if ('' !== $this->settings->getSetting('pvphardlimit', 0) && '0' !== $this->settings->getSetting('pvphardlimit', 0))
        {
            $wonexp = min($this->settings->getSetting('pvphardlimitamount', 15000), $wonexp);
        }

        if (15 == $badguy['creaturelevel'])
        {
            $wonexp = 0;
        }

        $lostexp = round($session['user']['experience'] * $this->settings->getSetting('pvpattlose', 15) / 100, 0);

        $args = new Character(['pvpmessageadd' => '', 'handled' => false, 'badguy' => $badguy]);
        $this->dispatcher->dispatch($args, Character::PVP_LOSS);
        $args = modulehook('pvploss', $args->getData());

        if ($character->getLevel() < $badguy['creaturelevel'])
        {
            $msg = 0;
            // if the player has leveled DOWN some how from when we started
            // attacking them, let's assume they DK'd, and these rewards are
            // way too rich for them.
            $this->battle->addContextToBattleEnd([
                'battle.defeated.level.down',
                [],
                self::TRANSLATION_DOMAIN,
            ]);
        }
        elseif (15 == $badguy['creaturelevel'])
        {
            $msg = 1;
        }
        else
        {
            $msg = 2;
            // Only give the reward if the person didn't level down
            $character->setGold($character->getGold() + $winamount)
                ->setExperience($character->getExperience() + $wonexp)
            ;

            $this->doctrine->persist($character);
            $this->doctrine->flush();
        }

        $subject = ['mail.defeated.subject', ['location' => $killedloc], self::TRANSLATION_DOMAIN];
        $message = [
            'mail.defeated.message',
            [
                'levelOpt'        => $msg,
                'location'        => $killedloc,
                'playerName'      => $session['user']['name'],
                'expWon'          => $wonexp,
                'goldWon'         => $winamount,
                'pvpMessageAdded' => $args['pvpmessageadd'],
                'date'            => $badguy['fightstartdate'],
                'dateRelative'    => $this->format->relativedate($badguy['fightstartdate']),
            ],
            self::TRANSLATION_DOMAIN,
        ];

        $this->systemMail->send($badguy['acctid'], $subject, $message);

        $session['user']['alive'] = false;

        $this->log->debug("started the fight and has been defeated by {$badguy['creaturename']} in {$killedloc} (lost {$session['user']['gold']} gold and {$lostexp} exp, victim tooks {$winamount} gold and {$wonexp} exp)", false, $session['user']['acctid']);
        $this->log->debug("was the victim and won aginst {$session['user']['name']} in {$killedloc} (earned {$winamount} gold and {$wonexp} exp)", false, $badguy['acctid']);

        $session['user']['gold']       = 0;
        $session['user']['hitpoints']  = 0;
        $session['user']['experience'] = round($session['user']['experience'] * (100 - $this->settings->getSetting('pvpattlose', 15)) / 100, 0);

        $this->battle->addContextToBattleEnd([
            'battle.defeated.death',
            ['creatureName' => $badguy['creaturename']],
            self::TRANSLATION_DOMAIN,
        ]);
        $this->battle->addContextToBattleEnd([
            'battle.defeated.gold',
            [],
            self::TRANSLATION_DOMAIN,
        ]);
        $this->battle->addContextToBattleEnd([
            'battle.defeated.experience',
            ['experience' => $this->settings->getSetting('pvpattlose', 15) / 100],
            self::TRANSLATION_DOMAIN,
        ]);
        $this->battle->addContextToBattleEnd([
            'battle.defeated.tomorrow',
            [],
            self::TRANSLATION_DOMAIN,
        ]);

        return $args['handled'];
    }
}
