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

trait Context
{
    private $context = [
        'messages'      => [],
        'encounter'     => [],
        'battle_start'  => [],
        'battle_rounds' => [],
        'battle_end'    => [],
    ];

    /**
     * Get context vars (results and info).
     */
    public function getContext(): array
    {
        $content = $this->context;

        $content['translation_domain'] = $this->getTranslationDomain();
        $content['enemies']            = $this->getEnemies();
        $content['bars_start']         = $this->getBattleBarsStart();
        $content['bars_end']           = $this->getBattleBarsEnd();
        $content['battle_has_winner']  = $this->battleHasWinner();

        return $content;
    }

    /**
     * Add messages to context.
     *
     * @param array|string|null $content
     */
    public function addContextToMessages($content): void
    {
        if (empty($content))
        {
            return;
        }

        $this->context['messages'][] = $content;
    }

    /**
     * Add content to round for enemy.
     *
     * @param array|string|null $content
     */
    public function addContextToRoundEnemy($content): void
    {
        if (empty($content))
        {
            return;
        }

        $this->context['battle_rounds'][$this->getRound()]['enemy'][] = $content;
    }

    /**
     * Add content to round for ally (player and companion).
     *
     * @param array|string|null $content
     */
    public function addContextToRoundAlly($content): void
    {
        if (empty($content))
        {
            return;
        }

        $this->context['battle_rounds'][$this->getRound()]['allied'][] = $content;
    }

    /**
     * Add content to battle end info.
     *
     * @param array|string|null $content
     */
    public function addContextToBattleEnd($content): void
    {
        if (empty($content))
        {
            return;
        }

        $this->context['battle_end'][] = $content;
    }
}
