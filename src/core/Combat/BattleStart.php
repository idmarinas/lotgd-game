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

trait BattleStart
{
    /**
     * Indicate if battle is started.
     *
     * @var bool
     */
    private $battleIsStarted    = false;
    private $battleIsInitalized = false;

    /**
     * Information of user.
     *
     * @var array
     */
    private $user;

    /**
     * Information of user without some data.
     *
     * @var array
     */
    private $userSafe;

    /**
     * Buffs of character.
     *
     * @var array
     */
    private $userBuffs = [];

    /**
     * Information of companions of user.
     *
     * @param bool $force caution with force initilize battle can delete data
     *
     * @var array
     */
    private $companions;

    public function initialize(bool $force = false): self
    {
        global $session, $companions;

        if ($this->battleIsInitalized && ! $force)
        {
            return $this;
        }

        $this->user     = $session['user'];
        $this->userSafe = array_filter($this->user, fn($key) => ! \in_array($key, [
            //-- Data of user
            'avatar', 'laston', 'loggedin', 'superuser', 'login', 'lastmotd', 'locked',
            'lastip', 'uniqueid', 'boughtroomtoday', 'emailaddress', 'replaceemail', 'emailvalidation', 'sentnotice', 'prefs',
            'transferredtoday', 'recentcomments', 'amountouttoday', 'regdate', 'banoverride', 'donation', 'donationspent',
            'donationconfig', 'referer', 'refererawarded', 'password', 'forgottenpassword', 'roles',
            //-- Other
            'acct', 'badguy', 'allowednavs', 'companions', 'bufflist',
        ]), ARRAY_FILTER_USE_KEY);
        $this->userBuffs  = $this->user['bufflist'] ?? [];
        $this->userBuffs  = \is_array($this->userBuffs) ? $this->userBuffs : [];
        $this->companions = $companions;

        $this->setOptions($this->user['badguy']['options'] ?? []);
        $this->setEnemies($this->user['badguy']['enemies'] ?? []);

        //-- If is forced not mark as initialized
        if (! $force)
        {
            $this->battleIsInitalized = true;
        }

        return $this;
    }

    /**
     * Initialize battle.
     */
    public function battleStart(): self
    {
        if ( ! $this->battleIsInitalized)
        {
            throw new LogicException('The battle cannot be start if it is not initiated first. Call Battle::initialize() before Battle::battleStart().');
        }

        $this->prepareFight(); //-- Prepares contenders for battle
        $this->autoTarget(); //-- Auto target first enemy alive

        //-- Change enemy
        if ($this->request->query->has('newtarget') && $newTarget = $this->request->query->getInt('newtarget'))
        {
            $this->changeTarget($newTarget);
            $this->isChangeTarget = true; //-- This avoid attacks
        }
        elseif ($skill = $this->request->query->get('skill', '') && 'fight' == $this->request->query->getAlnum('op', ''))
        {
            $this->applySkill($skill);
        }

        if ($this->getEnemiesCount())
        {
            $args = new GenericEvent(null, $this->enemies);
            $this->dispatcher->dispatch($args, Events::PAGE_BATTLE_TURN_START);
            modulehook('battle-turn-start', $args->getArguments());

            $this->setBattleBarStart($this->prepareBattleBars());
        }

        $this->suspendBuffs('pvp' == $this->options['type'] ? 'allowinpvp' : '');
        $this->suspendCompanions('pvp' == $this->options['type'] ? 'allowinpvp' : '');

        //-- Now that the bufflist is sane, see if we should add in the bodyguard.
        if ('pvp' == $this->options['type'] && 1 == $this->request->query->getInt('inn'))
        {
            $this->applyBodyguard($this->enemies[0]['bodyguardlevel']);
        }

        $this->isSurprised(); //-- Check if enemy surprise player

        //-- Battle is active
        $this->optionsBattleActive();

        $this->updateData();

        $this->battleIsStarted = true;

        return $this;
    }
}
