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
use Twig\TwigFunction;

class Navigation extends AbstractExtension
{
    use Pattern\AttributesString;
    use Pattern\Navigation;
    use PatternCore\Template;
    use PatternCore\Translator;
    use PatternCore\Navigation;


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('navigation_menu', [$this, 'display']),
            new TwigFunction('navigation_create_link', [$this, 'createLink']),
            new TwigFunction('navigation_create_header', [$this, 'createHeader']),
            new TwigFunction('navigation_pagination', [$this, 'showPagination']),
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
