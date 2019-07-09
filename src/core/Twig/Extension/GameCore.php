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

use Lotgd\Core\Pattern as PatternCore;
use Lotgd\Core\ServiceManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class GameCore extends AbstractExtension
{
    use PatternCore\Censor;
    use PatternCore\Container;
    use PatternCore\Sanitize;
    use PatternCore\Theme;
    use PatternCore\Translator;
    use Pattern\CoreFilter;
    use Pattern\CoreFunction;
    use Pattern\Mail;
    use Pattern\News;
    use Pattern\PageGen;
    use Pattern\Petition;
    use Pattern\Source;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->setContainer($serviceManager);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('colorize', [$this, 'colorize']),
            new TwigFilter('uncolorize', [$this, 'uncolorize']),
            new TwigFilter('prevent_codes', [$this, 'preventCodes']),
            new TwigFilter('nltoappon', [$this, 'nltoappon']),
            new TwigFilter('lotgd_url', [$this, 'lotgdUrl']),
            new TwigFilter('numeral', [$this, 'numeral']),
            new TwigFilter('relative_date', [$this, 'relativedate']),
            new TwigFilter('unserialize', 'unserialize'),
            new TwigFilter('stripslashes', 'stripslashes'),
            new TwigFilter('sprintfnews', [$this, 'sprintfnews']),
            new TwigFilter('censor', [$this, 'censor']),
            new TwigFilter('highlight_file', [$this, 'highlightFile']),
            new TwigFilter('highlight_string', [$this, 'highlightString'])
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

            new TwigFunction('page_title', [$this, 'pageTitle']),
            new TwigFunction('game_version', [$this, 'gameVersion']),
            new TwigFunction('game_copyright', [$this, 'gameCopyright']),
            new TwigFunction('game_source', [$this, 'gameSource']),
            new TwigFunction('game_page_gen', [$this, 'gamePageGen']),

            new TwigFunction('ye_olde_mail', [$this, 'yeOldeMail']),
            new TwigFunction('user_petition', [$this, 'userPetition']),
            new TwigFunction('admin_petition', [$this, 'adminPetition']),

            new TwigFunction('show_news_item', [$this, 'showNewsItem']),

            new TwigFunction('pvp_list_table', [$this, 'pvpListTable']),
            new TwigFunction('pvp_list_sleepers', [$this, 'pvpListSleepers']),

            new TwigFunction('var_dump', [$this, 'varDump']),
            new TwigFunction('bdump', [$this, 'bdump']),

            //-- Include a template from theme or module
            new TwigFunction('include_theme', [$this, 'includeThemeTemplate'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['all']]),
            new TwigFunction('include_module', [$this, 'includeModuleTemplate'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['all']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new TwigTest('array', function ($value) { return is_array($value); }),
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
