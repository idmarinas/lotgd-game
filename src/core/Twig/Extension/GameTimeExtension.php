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

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Tool\DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GameTimeExtension extends AbstractExtension
{
    private $dateTime;

    public function __construct(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('is_new_day', [$this->dateTime, 'isNewDay']),
            new TwigFunction('game_time_details', [$this->dateTime, 'gameTimeDetails']),
            new TwigFunction('gametime', [$this->dateTime, 'getGameTime']),
            new TwigFunction('game_time', [$this->dateTime, 'getGameTime']),
            new TwigFunction('secondstonextgameday', [$this->dateTime, 'secondsToNextGameDay']),
            new TwigFunction('seconds_to_next_game_day', [$this->dateTime, 'secondsToNextGameDay'])
        ];
    }
}
