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
use Lotgd\Core\Translator\Translator as CoreTranslator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Zend\Paginator\Paginator;

class Navigation extends AbstractExtension
{
    use Pattern\AttributesString;

    protected $navigation;
    protected $translator;
    protected $accesskeys;

    /**
     * @param CoreNavigation $navigation
     * @param CoreTranslator $translator
     * @param CoreAccessKeys $accesskeys
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
            new TwigFunction('navigation_create_header', [$this, 'createHeader']),
            new TwigFunction('navigation_pagination', [$this, 'showPagination']),
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
     * Create a link for menu.
     *
     * @param string $label
     * @param array  $options
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

        return \appoencode(sprintf('<%1$s %2$s>%4$s %3$s %5$s</%1$s>',
            (! $blocked ? 'a' : 'span'),
            $attributes,
            $label,
            $options['current']['open'] ?? '', //-- Open style to nav if is current
            $options['current']['close'] ?? '' //-- Close style to nav if is current
        ), true);
    }

    /**
     * Create a header section.
     *
     * @param string $label
     * @param array  $options
     *
     * @return string
     */
    public function createHeader($label, $options): string
    {
        if ($options['translate'] ?? false)
        {
            $label = $this->translator->trans($label, $options['params'] ?? [], $options['textDomain'] ?? 'navigation-app', $options['locale'] ?? null);
        }

        $attributes = $options['attributes'] ?? [];
        $attributes = $this->createAttributesString($attributes);

        return \sprintf('<%1$s %2$s>%3$s</%1$s>',
            (string) ($options['tag'] ?? 'span'),
            $attributes,
            appoencode($label, true)
        );
    }

    /**
     * Show pagination for a instance of Paginator.
     *
     * @param Paginator   $paginator
     * @param string      $link           Url to use in href atribute in links
     * @param string|null $template       You can change the template for your own if you need it at a specific time
     * @param string|null $scrollingStyle Options: All, Elastic, Jumping, Sliding. Default is Sliding
     * @param array|null  $params
     *
     * @return string
     */
    public function showPagination(Paginator $paginator, string $link, ?string $template = null, ?string $scrollingStyle = null, ?array $params = null): string
    {
        $template = $template ?: 'parts/pagination.twig';
        $scrollingStyle = $scrollingStyle ?: 'Sliding';

        $pages = get_object_vars($paginator->getPages($scrollingStyle));
        $link = $link.(false === \strpos($link, '#') ? '?' : '&');
        $pages['href'] = $link;

        if (null !== $params)
        {
            $pages = array_merge($pages, (array) $params);
        }

        return \LotgdTheme::renderThemeTemplate($template, $pages);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'navigation';
    }
}
