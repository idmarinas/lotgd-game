<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GameCore extends AbstractExtension
{
    use Pattern\CoreFilter;
    use Pattern\CoreFunction;
    use Pattern\Mail;
    use Pattern\Motd;
    use Pattern\PageGen;
    use Pattern\Petition;
    use Pattern\Source;

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('colorize', [$this, 'colorize']),
            new TwigFilter('uncolorize', [$this, 'uncolorize']),
            new TwigFilter('nltoappon', [$this, 'nltoappon']),
            new TwigFilter('lotgd_url', [$this, 'lotgdUrl']),
            new TwigFilter('numeral', [$this, 'numeral']),
            new TwigFilter('relativedate', [$this, 'relativedate']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getsetting', [$this, 'getsetting']),
            new TwigFunction('modulehook', [$this, 'modulehook']),
            new TwigFunction('is_valid_protocol', [$this, 'isValidProtocol']),
            new TwigFunction('gametime', [$this, 'gametime']),
            new TwigFunction('secondstonextgameday', [$this, 'secondstonextgameday']),

            new TwigFunction('game_version', [$this, 'gameVersion']),
            new TwigFunction('game_copyright', [$this, 'gameCopyright']),
            new TwigFunction('game_source', [$this, 'gameSource']),
            new TwigFunction('game_page_gen', [$this, 'gamePageGen']),

            new TwigFunction('message_of_the_day', [$this, 'messageOfTheDay']),
            new TwigFunction('ye_olde_mail', [$this, 'yeOldeMail']),
            new TwigFunction('user_petition', [$this, 'userPetition']),
            new TwigFunction('admin_petition', [$this, 'adminPetition']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'game-core';
    }
}
