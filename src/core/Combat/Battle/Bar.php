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

namespace Lotgd\Core\Combat\Battle;

trait Bar
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
    protected function prepareBattleBars(): array
    {
        $user = &$this->user; //fast and better
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
                'show_bar'       => false,
                'show_hp_text'   => true,
                'who'            => 'battlebars.who.enemy',
                'is_target'      => (isset($badguy['istarget']) && $badguy['istarget'] && $this->getEnemiesCount() > 1),
                'name'           => $ccode.$badguy['creaturename'].'`0',
                'level'          => $badguy['creaturelevel'],
                'hitpoints_text' => $hitpointstext,
                'health_text'    => $healthtext,
                'hp_value'       => $badguy['creaturehealth'], //-- Real health of creature
                'hp_total'       => $badguy['creaturemaxhealth'], //-- Real max health of creature
                'hp_value_text'  => $health,
                'hp_total_text'  => $maxhealth,
            ];

            $data['enemies'][$index] = array_merge($data['enemies'][$index], $barDisplay);
        }

        //-- Prepare data for companions
        $data['companions'] = [];

        foreach ($this->companions as $index => $companion)
        {
            $ccode = '`2';

            $health    = $companion['hitpoints'];
            $maxhealth = $companion['maxhitpoints'];

            if ($companion['hidehitpoints'] ?? false)
            {
                $maxhealth = $health = 'battlebars.unknownhp';
            }

            $data['companions'][$index] = [
                'show_bar'       => false,
                'show_hp_text'   => true,
                'who'            => 'battlebars.who.companion',
                'is_target'      => (isset($companion['istarget']) && $companion['istarget'] && $this->getEnemiesCount() > 1),
                'name'           => $ccode.$companion['name'].$ccode,
                'level'          => $user['level'],
                'hitpoints_text' => $hitpointstext,
                'health_text'    => $healthtext,
                'hp_value'       => $companion['hitpoints'], //-- Real health of companion
                'hp_total'       => $companion['maxhitpoints'], //-- Real max health of creature
                'hp_value_text'  => $health,
                'hp_total_text'  => $maxhealth,
            ];

            $data['companions'][$index] = array_merge($data['companions'][$index], $barDisplay);
        }

        //-- Prepare data for player
        $hitpointstext = $user['name'].'`0';

        $maxsoul = 0;
        if ( ! $user['alive'])
        {
            $hitpointstext = ['battlebars.death.player', ['name' => $user['name']]];
            $maxsoul       = 50 + 10 * $user['level'] + $user['dragonkills'] * 2;
        }

        $data['user'] = [
            'show_bar'      => false,
            'show_hp_text'  => true,
            'who'           => 'battlebars.who.player',
            'is_target'     => false,
            'name'          => $hitpointstext,
            'level'         => $user['level'],
            'health_text'   => $healthtext,
            'hp_value'      => $user['hitpoints'],
            'hp_total'      => ($user['alive'] ? $user['maxhitpoints'] : $maxsoul),
            'hp_value_text' => $user['hitpoints'],
            'hp_total_text' => ($user['alive'] ? $user['maxhitpoints'] : $maxsoul),
        ];

        $data['user'] = array_merge($data['user'], $barDisplay);

        return $data;
    }

    private function getPrefBarDisplay(): array
    {
        $barDisplay = (int) ($this->user['prefs']['forestcreaturebar'] ?? $this->settings->getSetting('forestcreaturebar', 0));

        $this->user['prefs']['forestcreaturebar'] = $barDisplay;

        $barDisplayOptions = [
            'show_bar'     => false,
            'show_hp_text' => true,
        ];

        if (1 == $barDisplay)
        {
            $barDisplayOptions['show_hp_text'] = false;
            $barDisplayOptions['show_bar']     = true;
        }
        elseif (2 == $barDisplay)
        {
            $barDisplayOptions['show_bar'] = true;
        }

        return $barDisplayOptions;
    }
}
