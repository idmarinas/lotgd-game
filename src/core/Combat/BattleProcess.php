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

namespace Lotgd\Core\Combat;

use LogicException;
use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

trait BattleProcess
{
    /**
     * Indicate if battle is processed.
     *
     * @var bool
     */
    protected $battleIsProcessed = false;

    /**
     * Process battle.
     */
    public function battleProcess(): self
    {
        if ( ! $this->battleIsStarted)
        {
            throw new LogicException('The battle cannot be processed if it is not initiated first. Call Battle::battleStart() before Battle::battleProcess().');
        }

        //-- Not process if player change enemy target
        if ($this->isChangeTarget)
        {
            return $this;
        }

        $count = $this->getAutoAttackCount();
        $op    = (string) $this->request->query->get('op', '');

        do
        {
            //-- We need to restore and calculate here to reflect changes that happen throughout the course of multiple rounds.
            $this->dispatcher->dispatch(new GenericEvent(), Events::PAGE_BATTLE_ROUND_START_BUFF_PRE);
            modulehook('startofround-prebuffs'); //-- For Stamina System

            $this->restoreBuffFields();
            $this->calculateBuffFields();
            $this->prepareCompanions();

            $roundAttacks = 0;
            //-- Run the beginning of round buffs (this also calculates all modifiers)
            foreach ($this->enemies as &$badguy)
            {
                if (
                    //-- Next enemy if this is dead
                    ( ! $this->isEnemyAlive($badguy))
                    //-- Next enemy if not is target and research max attacks per round
                    || ($roundAttacks > $this->getOption('maxattacks') && ! $badguy['istarget'])
                ) {
                    continue;
                }

                //-- Increased attacks in round if enemy not attack always
                if ( ! isset($badguy['alwaysattacks']) || ! $badguy['alwaysattacks'])
                {
                    ++$roundAttacks;
                }

                $this->buffModifiers = $this->activateBuffs('roundstart', $badguy);

                //-- Check health of player
                if ( ! $this->isPlayerAlive())
                {
                    //-- End Round attack
                    if ($this->surprised || 'run' == $op || 'fight' == $op)
                    {
                        $args = new GenericEvent(null, $badguy);
                        $this->dispatcher->dispatch($args, Events::PAGE_BATTLE_ROUND_END);
                        $badguy = modulehook('endofround', $args->getArguments()); //-- For Stamina System
                    }

                    //-- Break player cant continue
                    break;
                }

                //-- First move is for heal companion
                if ($this->isEnemyAlive($badguy) && $this->isPlayerAlive() && $badguy['istarget']) {
                    $this->companionHealer($badguy);
                }

                if ('fight' == $op || 'run' == $op || $this->surprised)
                {
                    if ( ! $this->surprised)
                    {
                        if ('fight' == $op)
                        {
                            //-- Second move is for magic companion
                            if ($this->isEnemyAlive($badguy) && $this->isPlayerAlive() && $badguy['istarget']) {
                                $this->companionMagic($badguy);
                            }
                            //-- Third move is for player
                            if ($this->isEnemyAlive($badguy) && $this->isPlayerAlive() && $badguy['istarget']) {
                                $this->playerMove($badguy);
                            }
                        }
                        elseif ('run' == $op)
                        {
                            $this->addContextToRoundAlly(['battle.run', ['creatureName' => $badguy['creaturename']], $this->getTranslationDomain()]);
                        }
                    }

                    //-- Fourth move is for enemy
                    if ($this->isEnemyAlive($badguy) && $this->isPlayerAlive() && $roundAttacks <= $this->getOption('maxattacks')) {
                        $this->enemyMove($badguy);
                    }

                    //-- Fifth move is for figther companion
                    if ($this->isEnemyAlive($badguy) && $this->isPlayerAlive() && $badguy['istarget']) {
                        $this->companionFighter($badguy);
                    }
                }

                $this->enemyAiScript($badguy);

                if ($this->surprised || 'run' == $op || 'fight' == $op)
                {
                    $badguy = modulehook('endofround', $badguy); //-- For Stamina System
                }
            } //-- foreach enemies
            unset($badguy);

            $this->expireBuffs();

            --$count; //-- Reduce movement count
            $this->increaseRound();

            $this->battleEnemyFleesIfAlone();
            $this->autoTarget(); //-- Change target if are dead
        } while ($count > 0 && $this->battleRoundContinue());

        $this->autoTarget(); //-- Auto target first enemy alive

        $args = new GenericEvent(null, $this->enemies);
        $this->dispatcher->dispatch($args, Events::PAGE_BATTLE_PAGE_END);
        modulehook('endofpage', $args->getArguments());

        $this->updateData();

        $this->battleIsProcessed = true;

        return $this;
    }
}
