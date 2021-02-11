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

trait BattleBar
{
    protected $battleBarStart = [];
    protected $battleBarEnd   = [];

    /**
     * Get info of all battle bars at start battle.
     */
    public function getBattleBarsStart(): array
    {
        return $this->battleBarStart;
    }

    /**
     * Get info of all battle bars at end battle.
     */
    public function getBattleBarsEnd(): array
    {
        return $this->battleBarEnd;
    }

    protected function setBattleBarStart(array $bars): self
    {
        $this->battleBarStart = $bars;

        return $this;
    }

    protected function setBattleBarEnd(array $bars): self
    {
        $this->battleBarEnd = $bars;

        return $this;
    }

    /**
     * Prepare info for battle bars information.
     */
    protected function prepareBattleBars()
    {
        global $companions, $session;

        $user = &$session['user']; //fast and better
        $data = [];

        $text          = ($user['alive'] ? 'alive' : 'death');
        $hitpointstext = "battlebars.{$text}.hitpoints";
        $healthtext    = "battlebars.{$text}.health";
        unset($text);

        $barDisplay = $this->getPrefBarDisplay();

        $data['enemies'] = [];
        //-- Prepare data for enemies
        foreach ($this->enemies as $index => $badguy)
        {
            $ccode = (($badguy['istarget'] ?? false) && $this->getEnemiesCount() > 1) ? '`#' : '`2';

            $health    = $badguy['creaturehealth'];
            $maxhealth = $badguy['creaturemaxhealth'];

            if ($badguy['hidehitpoints'] ?? false)
            {
                $maxhealth = $health = 'battlebars.unknownhp';
            }

            $data['enemies'][$index] = [
                'showbar'       => false,
                'showhptext'    => true,
                'who'           => 'battlebars.who.enemy',
                'isTarget'      => (isset($badguy['istarget']) && $badguy['istarget'] && $this->getEnemiesCount() > 1),
                'name'          => $ccode.$badguy['creaturename'].'`0',
                'level'         => $badguy['creaturelevel'],
                'hitpointstext' => $hitpointstext,
                'healthtext'    => $healthtext,
                'hpvalue'       => $badguy['creaturehealth'], //-- Real health of creature
                'hptotal'       => $badguy['creaturemaxhealth'], //-- Real max health of creature
                'hpvaluetext'   => $health,
                'hptotaltext'   => $maxhealth,
            ];

            $data['enemies'][$index] = \array_merge($data['enemies'][$index], $barDisplay);
        }

        //-- Prepare data for companions
        $data['companions'] = [];

        foreach ($companions as $index => $companion)
        {
            $ccode = '`2';

            $health    = $companion['hitpoints'];
            $maxhealth = $companion['maxhitpoints'];

            if ($companion['hidehitpoints'] ?? false)
            {
                $maxhealth = $health = 'battlebars.unknownhp';
            }

            $data['companions'][$index] = [
                'showbar'       => false,
                'showhptext'    => true,
                'who'           => 'battlebars.who.companion',
                'isTarget'      => (isset($companion['istarget']) && $companion['istarget'] && $this->getEnemiesCount() > 1),
                'name'          => $ccode.$companion['name'].$ccode,
                'level'         => $session['user']['level'],
                'hitpointstext' => $hitpointstext,
                'healthtext'    => $healthtext,
                'hpvalue'       => $companion['hitpoints'], //-- Real health of companion
                'hptotal'       => $companion['maxhitpoints'], //-- Real max health of creature
                'hpvaluetext'   => $health,
                'hptotaltext'   => $maxhealth,
            ];

            $data['companions'][$index] = \array_merge($data['companions'][$index], $barDisplay);
        }

        //-- Prepare data for player
        $hitpointstext = $user['name'].'`0';

        if ( ! $user['alive'])
        {
            $hitpointstext = ['battlebars.death.player', ['name' => $user['name']]];
            $maxsoul       = 50 + 10 * $user['level'] + $user['dragonkills'] * 2;
        }

        $data['user'] = [
            'showbar'     => false,
            'showhptext'  => true,
            'who'         => 'battlebars.who.player',
            'isTarget'    => false,
            'name'        => $hitpointstext,
            'level'       => $user['level'],
            'healthtext'  => $healthtext,
            'hpvalue'     => $user['hitpoints'],
            'hptotal'     => ( ! $user['alive'] ? $user['maxhitpoints'] : $maxsoul),
            'hpvaluetext' => $user['hitpoints'],
            'hptotaltext' => ( ! $user['alive'] ? $user['maxhitpoints'] : $maxsoul),
        ];

        $data['user'] = \array_merge($data['user'], $barDisplay);

        return $data;
    }

    private function getPrefBarDisplay()
    {
        global $session;

        $barDisplay = (int) ($session['user']['prefs']['forestcreaturebar'] ?? $this->settings->getSetting('forestcreaturebar', 0));

        $session['user']['prefs']['forestcreaturebar'] = $barDisplay;

        $barDisplayOptions = [
            'showbar'    => false,
            'showhptext' => true,
        ];

        if (1 == $barDisplay)
        {
            $barDisplayOptions['showhptext'] = false;
            $barDisplayOptions['showbar']    = true;
        }
        elseif (2 == $barDisplay)
        {
            $barDisplayOptions['showbar'] = true;
        }

        return $barDisplayOptions;
    }
}
