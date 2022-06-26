<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Twig\Extension\Pattern\CharacterFunction;
use Lotgd\Core\Twig\Extension\Pattern\CoreFilter;
use Lotgd\Core\Twig\Extension\Pattern\CoreFunction;
use Lotgd\Core\Twig\Extension\Pattern\Mail;
use Lotgd\Core\Twig\Extension\Pattern\News;
use Lotgd\Core\Twig\Extension\Pattern\PageGen;
use Lotgd\Core\Twig\Extension\Pattern\Petition;
use Lotgd\Core\Twig\Extension\Pattern\Source;
use Lotgd\Core\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Tool\Sanitize;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class GameCore extends AbstractExtension
{
    use CharacterFunction;
    use CoreFilter;
    use CoreFunction;
    use Mail;
    use News;
    use PageGen;
    use Petition;
    use Source;

    protected $request;
    protected $sanitize;
    protected $translator;
    protected $settings;
    protected $dispatcher;
    protected $doctrine;
    protected $session;

    public function __construct(
        Request $request,
        Sanitize $sanitize,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $doctrine,
        SessionInterface $session
    ) {
        $this->request    = $request;
        $this->sanitize   = $sanitize;
        $this->translator = $translator;
        $this->dispatcher = $dispatcher;
        $this->doctrine   = $doctrine;
        $this->session    = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('prevent_codes', [$this, 'preventCodes']),
            new TwigFilter('nltoappon', [$this, 'nltoappon']),
            new TwigFilter('unserialize', 'unserialize'),
            new TwigFilter('stripslashes', 'stripslashes'),
            new TwigFilter('sprintfnews', [$this, 'sprintfnews']),
            new TwigFilter('highlight_file', [$this, 'highlightFile']),
            new TwigFilter('highlight_string', [$this, 'highlightString']),
            new TwigFilter('yes_no', [$this, 'affirmationNegation']),
            new TwigFilter('affirmation_negation', [$this, 'affirmationNegation']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('url', [$this, 'baseUrl']),
            new TwigFunction('stimulus_url', fn(string $controller, string $method = 'index', string $query = ''): string => "stimulus.php?method={$method}&controller=".urlencode($controller).$query),

            new TwigFunction('trigger_event', [$this, 'triggerEvent']),
            new TwigFunction('modulehook', [$this, 'triggerEvent']), // Alias
            new TwigFunction('is_valid_protocol', [$this, 'isValidProtocol']),

            new TwigFunction('page_title', [$this, 'pageTitle']),
            new TwigFunction('game_version', fn(): string => Kernel::VERSION),
            new TwigFunction('game_copyright', fn(): string => Kernel::LICENSE.Kernel::COPYRIGHT),
            new TwigFunction('game_source', [$this, 'gameSource'], ['needs_environment' => true]),
            new TwigFunction('game_page_gen', [$this, 'gamePageGen'], ['needs_environment' => true]),

            new TwigFunction('ye_olde_mail', [$this, 'yeOldeMail'], ['needs_environment' => true]),
            new TwigFunction('user_petition', [$this, 'userPetition'], ['needs_environment' => true]),
            new TwigFunction('admin_petition', [$this, 'adminPetition'], ['needs_environment' => true]),

            new TwigFunction('show_news_item', [$this, 'showNewsItem']),

            new TwigFunction('pvp_list_table', [$this, 'pvpListTable'], ['needs_environment' => true]),
            new TwigFunction('pvp_list_sleepers', [$this, 'pvpListSleepers'], ['needs_environment' => true]),

            //-- Character functions
            new TwigFunction('character_race', [$this, 'characterRace']),

            //-- Other functions
            new TwigFunction('session_cookie_name', [$this, 'sessionCookieName']),
            new TwigFunction('var_dump', [$this, 'varDump']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new TwigTest('array', fn($value) => \is_array($value)),
            new TwigTest('object', fn($value) => \is_object($value)),
            new TwigTest('instanceof', fn($instance, $class) => $instance instanceof $class),
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
