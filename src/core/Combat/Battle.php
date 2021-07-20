<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Combat;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Tool\PlayerFunction;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Battle
{
    use BattleTrait;
    use BattleStart;
    use BattleProcess;
    use BattleEnd;
    use Battle\Buff;
    use Battle\Buffer;
    use Battle\Configuration;
    use Battle\Context;
    use Battle\Enemy;
    use Battle\Extended;
    use Battle\HealthBar;
    use Battle\Movement;
    use Battle\Option;
    use Battle\Other;
    use Battle\Prepare;
    use Battle\Process;
    use Battle\Result;
    use Battle\Round;
    use Battle\Skill;
    use Battle\Surprise;
    use Battle\Suspend;
    use Battle\Target;
    use Battle\TempStat;
    use Battle\TranslationDomain;

    private $dispatcher;
    private $doctrine;
    private $playerFunction;
    private $settings;
    private $request;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $doctrine,
        PlayerFunction $playerFunction,
        Settings $settings,
        Request $request
    ) {
        $this->dispatcher     = $dispatcher;
        $this->doctrine       = $doctrine;
        $this->playerFunction = $playerFunction;
        $this->settings       = $settings;
        $this->request        = $request;
    }

    /**
     * Get results of battle.
     *
     * @param bool $return Return content results of add to response (Default add results to response)
     *
     * @return void|string
     */
    public function battleResults(bool $return = false)
    {
        if ( ! $this->battleIsEnded)
        {
            throw new \LogicException('Can not get the results of battle. Call Battle::battleEnd() before Battle::battleResults().');
        }

        $content = $this->twig->render('page/battle.html.twig', $this->getContext());

        if ($return)
        {
            return $content;
        }

        $this->response->pageAddContent($content);
    }

    /**
     * Updated data of user and companions.
     */
    protected function updateData(): self
    {
        global $session, $companions;

        $companions = $this->companions;

        $session['user']               = $this->user;
        $session['user']['buffslist']  = $this->userBuffs;
        $session['user']['companions'] = $this->companions;
        $session['user']['badguy']     = [
            'enemies' => $this->getEnemies(),
            'options' => $this->getOptions(),
        ];

        return $this;
    }
}
