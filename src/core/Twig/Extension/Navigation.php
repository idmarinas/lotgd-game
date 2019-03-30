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

use Lotgd\Core\Navigation\AccessKeys as CoreAccessKeys;
use Lotgd\Core\Navigation\Navigation as CoreNavigation;
use Lotgd\Core\Pattern\Container;
use Lotgd\Core\ServiceManager;
use Lotgd\Core\Translator\Translator as CoreTranslator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Navigation extends AbstractExtension
{
    use Container;
    use Pattern\AttributesString;
    use Pattern\Navigation;

    protected $navigation;
    protected $translator;
    protected $accesskeys;

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
     * Get Navigation instance.
     *
     * @return CoreNavigation
     */
    public function getNavigation(): CoreNavigation
    {
        if (! $this->navigation instanceof CoreNavigation)
        {
            $this->navigation = $this->getContainer(CoreNavigation::class);
        }

        return $this->navigation;
    }

    /**
     * Get Navigation instance.
     *
     * @return CoreAccessKeys
     */
    public function getAccesskeys(): CoreAccessKeys
    {
        if (! $this->accesskeys instanceof CoreAccessKeys)
        {
            $this->accesskeys = $this->getContainer(CoreAccessKeys::class);
        }

        return $this->accesskeys;
    }

    /**
     * Get Translator instance.
     *
     * @return CoreTranslator
     */
    public function getTranslator(): CoreTranslator
    {
        if (! $this->translator instanceof CoreTranslator)
        {
            $this->translator = $this->getContainer(CoreTranslator::class);
        }

        return $this->translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'navigation';
    }
}
