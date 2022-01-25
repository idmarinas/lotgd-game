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

use Lotgd\Core\Twig\Extension\Pattern\AttributesString;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Navigation\AccessKeys;
use Lotgd\Core\Navigation\Navigation as NavigationCore;
use Lotgd\Core\Output\Format;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Navigation extends AbstractExtension
{
    use AttributesString;
    use Pattern\Navigation;

    protected $translator;
    protected $navigation;
    protected $accessKeys;
    protected $format;
    protected $request;

    public function __construct(
        TranslatorInterface $translator,
        NavigationCore $navigation,
        AccessKeys $accessKeys,
        Format $format,
        Request $request
    ) {
        $this->translator = $translator;
        $this->navigation = $navigation;
        $this->accessKeys = $accessKeys;
        $this->format     = $format;
        $this->request    = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('lotgd_url', [$this, 'lotgdUrl']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('navigation_menu', [$this, 'display'], ['needs_environment' => true]),
            new TwigFunction('navigation_create_link', [$this, 'createLink']),
            new TwigFunction('navigation_create_header', [$this, 'createHeader']),
            new TwigFunction('navigation_pagination', [$this, 'showPagination'], ['needs_environment' => true]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'navigation';
    }
}
