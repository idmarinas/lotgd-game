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

use Lotgd\Core\Translator\Translator as CoreTranslator;
use Lotgd\Core\Navigation\Navigation as CoreNavigation;
use Lotgd\Core\Navigation\AccessKeys as CoreAccessKeys;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Navigation extends AbstractExtension
{
    use Pattern\AttributesString;

    protected $navigation;
    protected $translator;
    protected $accesskeys;

    /**
     * @param CoreNavigation $navigation
     */
    public function __construct(CoreNavigation $navigation, CoreTranslator $translator, CoreAccessKeys $accesskeys)
    {
        $this->navigation = $navigation;
        $this->translator = $translator;
        $this->accesskeys = $accesskeys;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('navigation_menu', [$this, 'display']),
            new TwigFunction('navigation_create_link', [$this, 'createLink']),
        ];
    }

    /**
     * Display navigation menu.
     *
     * @return string
     */
    public function display()
    {
        return \LotgdTheme::renderThemeTemplate('parts/navigation.twig', [
            'navigation' => $this->navigation->getNavigation(),
            'headers' => $this->navigation->getHeaders(),
            'navs' => $this->navigation->getNavs()
        ]);
    }

    /**
     * Create a link for menu
     *
     * @param string $label
     * @param array $options
     *
     * @return string
     */
    public function createLink($label, $options)
    {
        if ($options['translate'] ?? false)
        {
            $label = $this->translator->trans($label, $options['params'] ?? [], $options['textDomain'] ?? 'navigation-app', $options['locale'] ?? null);
        }

        $attributes = $options['attributes'] ?? [];
        if (! $blocked = $this->navigation->isBlocked($options['link']))
        {
            $label = $this->accesskeys->create($label, $attributes);
        }
        if ($blocked)
        {
            unset($attributes['href']);
        }
        $attributes = $this->createAttributesString($attributes);

        return \sprintf('<%1$s %2$s>%3$s</%1$s>',
            (! $blocked ? 'a' : 'span'),
            $attributes,
            appoencode($label, true)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'navigation';
    }
}
