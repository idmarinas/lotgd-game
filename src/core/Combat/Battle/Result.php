<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

use Lotgd\Core\Event\Fight;
use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

trait Result
{
    protected $battleHasWinner = false;
    protected $victory         = false;
    protected $defeat          = false;

    public function battleHasWinner(): bool
    {
        return $this->battleHasWinner;
    }

    public function isVictory(): bool
    {
        return $this->victory;
    }

    public function isDefeat(): bool
    {
        return $this->defeat;
    }

    protected function processBatteResults(): void
    {
        if ($this->battleHasWinner())
        {
            // expire any buffs which cannot persist across fights
            $this->expireBuffsAfterbattle();
            //unsuspend any suspended buffs
            $this->unsuspendBuffs((('pvp' == $this->getOptionType()) ? 'allowinpvp' : false));

            $this->user['alive'] && $this->unsuspendCompanions(('pvp' == $this->getOptionType()) ? 'allowinpvp' : false);

            $this->companions = array_filter($this->companions, function ($companion)
            {
                if (isset($companion['expireafterfight']) && $companion['expireafterfight'])
                {
                    $companion['dyingtext'] && $this->addContextToBattleEnd($companion['dyingtext']);

                    return false;
                }

                return true;
            });

            if ($this->isVictory())
            {
                $args = new GenericEvent(null, ['enemies' => $this->getEnemies(), 'options' => $this->getOptions(), 'messages' => []]);
                $this->dispatcher->dispatch($args, Events::PAGE_BATTLE_END_VICTORY);
                $result        = $args->getArguments();
                $this->enemies = $result['enemies'];

                $this->context['battle_end'] += $result['messages'];

                $this->allowProccessBatteResults() && $this->battleVictory();
            }
            elseif ($this->isDefeat())
            {
                $args = new GenericEvent(null, ['enemies' => $this->getEnemies(), 'options' => $this->getOptions(), 'messages' => []]);
                $this->dispatcher->dispatch($args, Events::PAGE_BATTLE_END_DEFEAT);
                $result        = $args->getArguments();
                $this->enemies = $result['enemies'];

                $this->context['battle_end'] += $result['messages'];

                $this->allowProccessBatteResults() && $this->battleDefeat();
            }
        }

        if ($this->countEnemiesAlive() > 0)
        {
            $args = new GenericEvent(null, $this->getEnemies());
            $this->dispatcher->dispatch($args, Events::PAGE_BATTLE_TURN_END);
        }

        $this->setBattleBarEnd($this->prepareBattleBars());
    }

    protected function battleVictory()
    {
        $result = $this->proccessEnemiesResult();

        $didDamage     = $result['didDamage'];
        $creatureLevel = $result['creatureLevel'];
        $gold          = $result['gold'];
        $exp           = $result['exp'];
        $expBonus      = $result['expBonus'];
        $denyflawless  = $result['denyFlawless'];

        //-- Proccess find a gem only in forest
        $this->canGainGem() && $this->victoryProccessGemFind();
        //-- Gold for user only in forest
        if ($this->canGainGold() && $gold)
        {
            $this->victoryProccessGoldFind($gold);
        }

        //-- Gain experience if can gain exp and not favor
        if ($this->canGainExp() && ! $this->canGainFavor())
        {
            $this->victoryGainExperience($exp, $expBonus);
        }
        //-- Gain favor if can gain favor and not exp
        if ($this->canGainFavor() && ! $this->canGainExp())
        {
            $this->victoryGainFavor($exp, $expBonus);
        }

        //-- Perfect battle
        if ( ! $didDamage)
        {
            $this->addContextToBattleEnd(['combat.end.flawless', [], $this->getTranslationDomain()]);

            if ($denyflawless)
            {
                $this->addContextToBattleEnd("`c`\${$denyflawless}`0´c");
            }
            elseif ($this->user['level'] <= $creatureLevel)
            {
                //-- Only when can gain favor
                if ($this->canGainFavor())
                {
                    $this->addContextToBattleEnd(['combat.end.get.torment', [], $this->getTranslationDomain()]);
                    ++$this->user['gravefights'];
                }
                else
                {
                    $this->addContextToBattleEnd(['combat.end.get.turn', [], $this->getTranslationDomain()]);
                    ++$this->user['turns'];
                }
            }
            elseif ($this->canGainExp())
            {
                $this->addContextToBattleEnd(['combat.end.forget.turn', [], $this->getTranslationDomain()]);
            }
            elseif ($this->canGainFavor())
            {
                $this->addContextToBattleEnd(['combat.end.forget.torment', [], $this->getTranslationDomain()]);
            }

            $this->addContextToBattleEnd('`n');
        }

        if ($this->user['hitpoints'] <= 0)
        {
            $this->addContextToBattleEnd(['combat.end.negative.hitpoints', [], $this->getTranslationDomain()]);
            $this->user['hitpoints'] = 1;
        }
    }

    protected function battleDefeat()
    {
        $percent = $this->settings->getSetting('forestexploss', 10);
        $killer  = false;

        foreach ($this->enemies as $badguy)
        {
            if ($badguy['killedplayer'] ?? false)
            {
                $killer = $badguy;
            }

            if ($badguy['creaturewin'] ?? false)
            {
                $this->addContextToBattleEnd($this->tools->substitute(
                    "`b`&{$badguy['creaturewin']}`0´b`n",
                    ...$this->getSubstituteParams($badguy)
                ));
            }
        }

        if ($killer)
        {
            $this->addContextToBattleEnd(['combat.end.defeated.die', ['creatureName' => $killer['creaturename']], $this->getTranslationDomain()]);
        }

        //-- If not want add a news when defeat use ::disableCreateNews()
        if ($this->allowCreateNews())
        {
            $params = [
                //-- The monster's name (also can be specified as badGuy
                'badGuyName' => $killer['creaturename'],
                'badGuy'     => $killer['creaturename'],
                //-- The monster's weapon (also can be specified as creatureWeapon
                'badGuyWeapon'   => $killer['creatureweapon'],
                'creatureWeapon' => $killer['creatureweapon'],
            ];

            $deathmessage = $this->tools->selectDeathMessage($this->getBattleZone(), $params);
            $taunt        = $this->tools->selectTaunt($params);

            $this->tools->addNews('deathmessage', [
                'deathmessage' => $deathmessage,
                'taunt'        => $taunt,
            ], '');
        }

        if ($this->canLostGold())
        {
            $this->log->debug("lost gold when they were slain {$this->getBattleZone()}", false, false, 'forestlose', -$this->user['gold']);
            $this->user['gold'] = 0;

            $this->addContextToBattleEnd(['combat.end.defeated.lost.gold', [], $this->getTranslationDomain()]);
        }

        if ($this->canLostExp())
        {
            $this->user['experience'] = round($this->user['experience'] * (1 - ($percent / 100)), 0);

            $this->addContextToBattleEnd(['combat.end.defeated.lost.exp', ['percent' => ($percent / 100)], $this->getTranslationDomain()]);
        }

        if ($this->canDie())
        {
            $this->navigation->addNav('battle.nav.news', 'news.php', ['textDomain' => 'navigation_app']);

            $this->user['alive']     = false;
            $this->user['hitpoints'] = 0;
            $this->addContextToBattleEnd(['combat.end.defeated.tomorrow.forest', [], $this->getTranslationDomain()]);
        }
        elseif ('graveyard' === $this->getBattleZone())
        {
            $this->navigation->addNav('battle.nav.graveyard', 'graveyard.php', ['textDomain' => 'navigation_app']);

            $this->user['gravefights'] = 0;
            $this->addContextToBattleEnd(['combat.end.defeated.tomorrow.graveyard', [], $this->getTranslationDomain()]);
        }
    }

    protected function proccessEnemiesResult(): array
    {
        $count         = \count($this->getEnemies());
        $creatureLevel = 0;
        $didDamage     = false;
        $denyFlawless  = '';
        $gold          = 0;
        $exp           = 0;
        $expBonus      = 0;

        foreach ($this->enemies as &$badguy)
        {
            $badguy['creaturegold'] = e_rand(0, $badguy['creaturegold']);

            if ($this->settings->getSetting('dropmingold', 0))
            {
                $badguy['creaturegold'] = e_rand(round($badguy['creaturegold'] / 4), round(3 * $badguy['creaturegold'] / 4));
            }
            $gold += $badguy['creaturegold'];

            if ($badguy['creaturelose'] ?? false)
            {
                $this->addContextToBattleEnd($this->tools->substitute(
                    $badguy['creaturelose'].'`n',
                    ...$this->getSubstituteParams($badguy)
                ));
            }

            $slain = $this->canGainFavor() ? 'tormented' : 'slain';
            $this->addContextToBattleEnd(['combat.end.'.$slain, ['creatureName' => $badguy['creaturename']], $this->getTranslationDomain()]);

            // If any creature did damage, we have no flawless fight. Easy as that.
            if ($badguy['diddamage'] ?? false)
            {
                $didDamage = true;
            }
            $creatureLevel = max($creatureLevel, $badguy['creaturelevel']);

            if ($this->allowFlawless() && isset($badguy['denyflawless']) && $badguy['denyflawless'] > '')
            {
                $denyFlawless = $badguy['denyflawless'];
            }

            $exp      += $badguy['creatureexp'];
            $expBonus += round(($badguy['creatureexp'] * (1 + .25 * ($badguy['creaturelevel'] - $this->user['level']))) - $badguy['creatureexp'], 0);
        }
        unset($badguy);

        $multibonus = $count > 1 ? 1 : 0;
        $expBonus += $this->user['dragonkills'] * $this->user['level'] * $multibonus;

        // We now have the total experience which should have been gained during the fight.
        // Now we will calculate the average exp per enemy.
        $exp      = round($exp / $count);
        $gold     = e_rand(round($gold / $count), round(($gold / $count) * (($count + 1) * pow(1.2, $count - 1)), 0));
        $expBonus = round($expBonus / $count, 0);

        // Increase the level for each enemy by one half, so flawless fights can be achieved for
        // fighting multiple low-level critters
        $creatureLevel += (0.5 * ($count - 1));

        return [
            'didDamage'     => $didDamage,
            'creatureLevel' => $creatureLevel,
            'gold'          => $gold,
            'exp'           => $exp,
            'expBonus'      => $expBonus,
            'denyFlawless'  => $denyFlawless,
        ];
    }

    protected function victoryProccessGemFind(): void
    {
        //-- No gem hunters allowed!
        $args = new Fight(['chance' => $this->settings->getSetting('forestgemchance', 25)]);
        $this->dispatcher->dispatch($args, Fight::ALTER_GEM_CHANCE);
        $args       = $args->getData();
        $gemchances = max(0, $args['chance']);

        //-- Gems only find in forest
        if ($this->user['level'] < $this->settings->getSetting('maxlevel', 15) && 1 == mt_rand(0, $gemchances))
        {
            $this->addContextToBattleEnd(['combat.end.get.gem', [], $this->getTranslationDomain()]);
            ++$this->user['gems'];
            $this->log->debug('found gem when slaying a monster.', false, false, 'forestwingem', 1);
        }
    }

    protected function victoryProccessGoldFind($gold): void
    {
        $this->addContextToBattleEnd(['combat.end.get.gold', ['gold' => $gold], $this->getTranslationDomain()]);
        $this->user['gold'] += $gold;
        $this->log->debug('received gold for slaying a monster.', false, false, 'forestwin', $gold);
    }

    protected function victoryGainExperience($exp, $expbonus): void
    {
        $count = \count($this->enemies);

        if (floor($exp + $expbonus) < 0)
        {
            $expbonus = -$exp + 1;
        }

        if ($expbonus > 0)
        {
            $expbonus = round($expbonus * pow(1 + ($this->settings->getSetting('addexp', 5) / 100), $count - 1), 0);

            $this->addContextToBattleEnd(['combat.end.experience.forest.bonus', [
                'bonus'     => $expbonus,
                'calculate' => true,
                'exp'       => $exp,
                'totalExp'  => $exp + $expbonus,
            ], $this->getTranslationDomain()]);
        }
        elseif ($expbonus < 0)
        {
            $this->addContextToBattleEnd(['combat.end.experience.forest.penalize', [
                'bonus'     => abs($expbonus),
                'calculate' => true,
                'exp'       => $exp,
                'totalExp'  => $exp + $expbonus,
            ], $this->getTranslationDomain()]);
        }

        $totalExp = ($exp + $expbonus);
        //-- Only show if win Exp
        if ($totalExp)
        {
            $this->addContextToBattleEnd(['combat.end.experience.forest.total.exp', ['experience' => $totalExp], $this->getTranslationDomain()]);
            $this->user['experience'] += $totalExp;
        }
    }

    protected function victoryGainFavor($exp, $expbonus): void
    {
        if (floor($exp + $expbonus) < 0)
        {
            $expbonus = -$exp + 1;
        }

        $count = \count($this->enemies);

        if ($expbonus > 0)
        {
            $expbonus = round($expbonus * pow(1 + ($this->settings->getSetting('addexp', 5) / 100), $count - 1), 0);

            $this->addContextToBattleEnd(['combat.end.experience.graveyard.bonus', [
                'calculate' => true,
                'bonus'     => $expbonus,
                'exp'       => $exp,
                'totalExp'  => $exp + $expbonus,
            ], $this->getTranslationDomain()]);
        }
        elseif ($expbonus < 0)
        {
            $this->addContextToBattleEnd(['combat.end.experience.graveyard.penalize', [
                'calculate' => true,
                'bonus'     => abs($expbonus),
                'exp'       => $exp,
                'totalExp'  => $exp + $expbonus,
            ], $this->getTranslationDomain()]);
        }

        $totalExp = ($exp + $expbonus);
        //-- Only show if win Exp/favor
        if ($totalExp)
        {
            $this->addContextToBattleEnd(['combat.end.experience.graveyard.total.favor', [
                'favor'              => $totalExp,
                'graveyardOwnerName' => (string) $this->settings->getSetting('deathoverlord', '`$Ramius`0'),
            ], $this->getTranslationDomain()]);
            $this->user['deathpower'] += $totalExp;
        }
    }
}
