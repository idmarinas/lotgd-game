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
    use BattleProcess;
    use BattleStart;
    use BattleEnd;
    use Battle\Action;
    // use Battle\Bar;
    use Battle\Buff;
    // use Battle\Buffer;
    // use Battle\Context;
    use Battle\Enemy;
    use Battle\Extended;
    use Battle\Option;
    use Battle\Other;
    // use Battle\Prepare;
    use Battle\Skill;
    // use Battle\Surprise;
    // use Battle\Suspend;
    use Battle\Target;
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
     * Only get results not print results.
     */
    public function battleResults(): string
    {
        if ( ! $this->battleIsEnded)
        {
            throw new \LogicException('Can not get the results of battle. Call Battle::battleEnd() before Battle::battleResults().');
        }

        return $this->twig->render('page/battle.html.twig', $this->getContext());
    }

    /**
     * Updated data of user and companions.
     */
    protected function updateData(): self
    {
        global $session, $companions;

        $session['user'] = $this->user;
        $companions      = $this->companions;

        $session['user']['badguy'] = [
            'enemies' => $this->getEnemies(),
            'options' => $this->getOptions(),
        ];

        return $this;
    }
}
