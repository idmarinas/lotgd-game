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
use LogicException;
use Lotgd\Core\Combat\Battle\Buff;
use Lotgd\Core\Combat\Battle\Buffer;
use Lotgd\Core\Combat\Battle\Configuration;
use Lotgd\Core\Combat\Battle\Context;
use Lotgd\Core\Combat\Battle\Enemy;
use Lotgd\Core\Combat\Battle\Extended;
use Lotgd\Core\Combat\Battle\Formula;
use Lotgd\Core\Combat\Battle\Ghost;
use Lotgd\Core\Combat\Battle\HealthBar;
use Lotgd\Core\Combat\Battle\Menu;
use Lotgd\Core\Combat\Battle\Movement;
use Lotgd\Core\Combat\Battle\Option;
use Lotgd\Core\Combat\Battle\Other;
use Lotgd\Core\Combat\Battle\Prepare;
use Lotgd\Core\Combat\Battle\Process;
use Lotgd\Core\Combat\Battle\Result;
use Lotgd\Core\Combat\Battle\Round;
use Lotgd\Core\Combat\Battle\Skill;
use Lotgd\Core\Combat\Battle\Surprise;
use Lotgd\Core\Combat\Battle\Suspend;
use Lotgd\Core\Combat\Battle\Target;
use Lotgd\Core\Combat\Battle\TempStat;
use Lotgd\Core\Combat\Battle\TranslationDomain;
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
    use Buff;
    use Buffer;
    use Configuration;
    use Context;
    use Enemy;
    use Extended;
    use Formula;
    use Ghost;
    use HealthBar;
    use Menu;
    use Movement;
    use Option;
    use Other;
    use Prepare;
    use Process;
    use Result;
    use Round;
    use Skill;
    use Surprise;
    use Suspend;
    use Target;
    use TempStat;
    use TranslationDomain;

    private $dispatcher;
    private $doctrine;
    private $playerFunction;
    private $settings;
    private $request;
    private $battleShowedResults = false;

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
            throw new LogicException('Can not get the results of battle. Call Battle::battleEnd() before Battle::battleResults().');
        }

        $content = $this->twig->render('battle/index.html.twig', $this->getContext());

        $this->battleShowedResults = true;

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

        //-- Not overwrite allowednavs
        $allowedNavs = $session['user']['allowednavs'];

        $companions = $this->companions;

        $session['user']               = $this->user;
        $session['user']['buffslist']  = $this->userBuffs;
        $session['user']['companions'] = $this->companions;
        $session['user']['badguy']     = [
            'enemies' => $this->getEnemies(),
            'options' => $this->getOptions(),
        ];
        $session['user']['allowednavs'] = $allowedNavs;

        return $this;
    }

    protected function getSubstituteParams(array $badguy, array $search = [], array $replace = []): array
    {
        $search = array_merge([
            '{badguyweapon}',
            '{badguyname}',
            '{badguy}',
            '{creatureweapon}',
        ], $search);

        $replace = array_merge([
            $badguy['creatureweapon'],
            $badguy['creaturename'],
            $badguy['creaturename'],
            $badguy['creatureweapon'],
        ], $replace);

        return [
            $search,
            $replace,
        ];
    }
}
