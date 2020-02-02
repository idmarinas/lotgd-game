<?php

// translator ready
// addnews ready
// mail ready

require_once 'lib/pvpwarning.php';
require_once 'lib/substitute.php';
require_once 'lib/systemmail.php';
require_once 'lib/datetime.php';

/**
 * This contains functions to support pvp.
 *
 * @param int $characterId
 *
 * @return bool|array
 */
function setup_pvp_target(int $characterId)
{
    global $session, $textDomain;

    $pvptime = getsetting('pvptimeout', 600);
    $pvptimeout = date('Y-m-d H:i:s', strtotime("-$pvptime seconds"));

    $repository = \Doctrine::getRepository('LotgdCore:Characters');
    $entity = $repository->extractEntity($repository->getCharacterForPvp($characterId));

    $message = 'flash.message.pvp.start.not.found';

    if ($entity)
    {
        $message = 'flash.message.pvp.start.tired';

        if (abs($session['user']['level'] - $entity['creaturelevel']) > getsetting('pvprange', 2))
        {
            $message = 'flash.message.pvp.start.out.range';
        }
        elseif ($entity['pvpflag'] > $pvptimeout)
        {
            $message = 'flash.message.pvp.start.pvp.timeout';
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
            $entityCharacter->setPvpflag(new \DateTime('now'));

            \Doctrine::persist($entityCharacter);
            \Doctrine::flust();

            $entity['creatureexp'] = round($entity['creatureexp'], 0);
            $entity['playerstarthp'] = $session['user']['hitpoints'];
            $entity['fightstartdate'] = new \DateTime('now');
            $entity = modulehook('pvpadjust', $entity);

            pvpwarning(true);

            return $entity;
        }
    }

    return $message;
}

function pvpvictory($badguy, $killedloc)
{
    global $session, $lotgdBattleContent, $textDomain;

    // If the victim has logged on and banked some, give the lessor of the gold amounts.
    $repository = \Doctrine::getRepository('LotgdCore:Characters');
    $character = $repository->find($badguy['character_id']);

    $badguy['creaturegold'] = ($character->getGold() > (int) $badguy['creaturegold'] ? (int) $badguy['creaturegold'] : $character->getGold());

    if (15 == $session['user']['level'])
    {
        $lotgdBattleContent['battleend'][] = [
            'battle.max.level',
            [],
            $textDomain
        ];
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

    $lotgdBattleContent['battleend'][] = [
        'battle.victory.creature',
        [ 'creatureName' => $badguy['creaturename'] ],
        $textDomain
    ];
    $lotgdBattleContent['battleend'][] = [
        'battle.victory.gold',
        [ 'gold' => $winamount ],
        $textDomain
    ];

    $exp = round(getsetting('pvpattgain', 10) * $badguy['creatureexp'] / 100, 0);

    if (getsetting('pvphardlimit', 0))
    {
        $exp = min(getsetting('pvphardlimitamount', 15000), $exp);
    }

    if (15 == $session['user']['level'])
    {
        $exp = 0;
    }

    $expbonus = round(($exp * (1 + .1 * ($badguy['creaturelevel'] - $session['user']['level']))) - $exp, 0);

    if ($expbonus > 0)
    {
        $lotgdBattleContent['battleend'][] = [
            'battle.victory.difficult',
            [ 'experience' =>  $expbonus ],
            $textDomain
        ];
    }
    elseif ($expbonus < 0)
    {
        $lotgdBattleContent['battleend'][] = [
            'battle.victory.simplistic',
            [ 'experience' =>  abs($expbonus) ],
            $textDomain
        ];
    }
    $wonexp = $exp + $expbonus;
    $lotgdBattleContent['battleend'][] = [
        'battle.victory.experience',
        [ 'experience' => $wonexp ],
        $textDomain
    ];
    $session['user']['experience'] += $wonexp;

    $lostexp = round($badguy['creatureexp'] * getsetting('pvpdeflose', 5) / 100, 0);

    //player wins gold and exp from badguy
    debuglog("started the fight and defeated {$badguy['creaturename']} in $killedloc (earned $winamount of {$badguy['creaturegold']} gold and $wonexp of $lostexp exp)", false, $session['user']['acctid']);
    debuglog("was victim and has been defeated by {$session['user']['name']} in $killedloc (lost {$badguy['creaturegold']} gold and $lostexp exp, actor tooks $winamount gold and $wonexp exp)", false, $badguy['acctid']);

    $args = ['pvpmessageadd' => '', 'handled' => false, 'badguy' => $badguy];
    $args = modulehook('pvpwin', $args);


    $subject = [ 'mail.victory.subject', [ 'location' => $killedloc ], $textDomain ];

    $message = [
        'mail.victory.message',
        [
            'location' => $killedloc,
            'playerName' => $session['user']['name'],
            'playerWeapon' => $session['user']['weapon'],
            'creatureStartHp' => $badguy['playerstarthp'],
            'playerHitpoints' => $session['user']['hitpoints'],
            'expLose' => $lostexp,
            'goldLost' => $badguy['creaturegold'],
            'pvpDefLose' => ((int) getsetting('pvpdeflose', 5) / 100),
            'pvpMessageAdded' => $args['pvpmessageadd'],
            'date' => $badguy['fightstartdate'],
            'dateRelative' => \LotgdFormat::relativedate($badguy['fightstartdate']),
            //-- Subjective pronoun for the player (him her)
            'himHer' => $session['user']['sex'] ? 'her' : 'him',
            //-- Possessive pronoun for the player (his her)
            'hisHer' => $session['user']['sex'] ? 'her' : 'his',
            //-- Objective pronoun for the player (he she)
            'heShe' => $session['user']['sex'] ? 'she' : 'he',
        ],
        $textDomain,
    ];

    // /\- Gunnar Kreitz
    systemmail($badguy['acctid'], $subject, $message);
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

    $character->setExperience(max($character->getExperience() - $lostexp), 0);

    \Doctrine::persist($character);
    \Doctrine::flush();

    return $args['handled'];
}

function pvpdefeat($badguy, $killedloc)
{
    global $session, $lotgdBattleContent, $textDomain;

    \LotgdNavigation::addNav('battle.nav.news', 'news.php');

    $badguy['acctid'] = (int) $badguy['acctid'];
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

    $repository = \Doctrine::getRepository('LotgdCore:Characters');
    $character = $repository->find($badguy['character_id']);

    $wonexp = round($session['user']['experience'] * getsetting('pvpdefgain', 10) / 100, 0);

    if (getsetting('pvphardlimit', 0))
    {
        $wonexp = min(getsetting('pvphardlimitamount', 15000), $wonexp);
    }

    if (15 == $badguy['creaturelevel'])
    {
        $wonexp = 0;
    }

    $lostexp = round($session['user']['experience'] * getsetting('pvpattlose', 15) / 100, 0);

    $args = ['pvpmessageadd' => '', 'handled' => false, 'badguy' => $badguy];
    $args = modulehook('pvploss', $args);

    if ($character->getLevel() < $badguy['creaturelevel'])
    {
        $msg = 0;
        // if the player has leveled DOWN some how from when we started
        // attacking them, let's assume they DK'd, and these rewards are
        // way too rich for them.
        $lotgdBattleContent['battleend'][] = [
            'battle.defeated.level.down',
            [],
            $textDomain
        ];
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

        \Doctrine::persist($character);
        \Doctrine::flush();
    }

    $subject = ['mail.defeated.subject', [ 'location' => $killedloc ], $textDomain];
    $message = [
        'mail.defeated.message',
        [
            'levelOpt' => $msg,
            'location' => $killedloc,
            'playerName' => $session['user']['name'],
            'expWon' => $wonexp,
            'goldWon' => $winamount,
            'pvpMessageAdded' => $args['pvpmessageadd'],
            'date' => $badguy['fightstartdate'],
            'dateRelative' => \LotgdFormat::relativedate($badguy['fightstartdate'])
        ],
        $textDomain,
    ];

    systemmail($badguy['acctid'], $subject, $message);

    $session['user']['alive'] = false;

    debuglog("started the fight and has been defeated by {$badguy['creaturename']} in $killedloc (lost {$session['user']['gold']} gold and $lostexp exp, victim tooks $winamount gold and $wonexp exp)", false, $session['user']['acctid']);
    debuglog("was the victim and won aginst {$session['user']['name']} in $killedloc (earned $winamount gold and $wonexp exp)", false, $badguy['acctid']);

    $session['user']['gold'] = 0;
    $session['user']['hitpoints'] = 0;
    $session['user']['experience'] = round($session['user']['experience'] * (100 - getsetting('pvpattlose', 15)) / 100, 0);
    $lotgdBattleContent['battleend'][] = [
        'battle.defeated.death',
        [ 'creatureName' => $badguy['creaturename'] ],
        $textDomain
    ];
    $lotgdBattleContent['battleend'][] = [
        'battle.defeated.gold',
        [],
        $textDomain
    ];
    $lotgdBattleContent['battleend'][] = [
        'battle.defeated.experience',
        [ 'experience' => getsetting('pvpattlose', 15) / 100 ],
        $textDomain
    ];
    $lotgdBattleContent['battleend'][] = [
        'battle.defeated.tomorrow',
        [],
        $textDomain
    ];

    return $args['handled'];
}
