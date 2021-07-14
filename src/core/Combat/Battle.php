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
    use Battle\Buff;
    use Battle\Extended;
    use Battle\Skill;

    private $buffer;
    private $dispatcher;
    private $doctrine;
    private $playerFunction;
    private $settings;
    private $request;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $doctrine,
        Buffer $buffer,
        PlayerFunction $playerFunction,
        Settings $settings,
        Request $request
    ) {
        $this->buffer         = $buffer;
        $this->dispatcher     = $dispatcher;
        $this->doctrine       = $doctrine;
        $this->playerFunction = $playerFunction;
        $this->settings       = $settings;
        $this->request        = $request;
    }
}
