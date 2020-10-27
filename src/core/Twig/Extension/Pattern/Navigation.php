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

namespace Lotgd\Core\Twig\Extension\Pattern;

use Laminas\Paginator\Paginator;

/**
 * Trait to navigation display.
 */
trait Navigation
{
    /**
     * Display navigation menu.
     *
     * @return string
     */
    public function display()
    {
        $template = $this->getTemplate()->load("@theme{$this->getTemplate()->getThemeNamespace()}/_blocks/_partials.html.twig");

        return $template->renderBlock('navigation_menu', [
            'navigation' => $this->getNavigation()->getNavigation(),
            'headers'    => $this->getNavigation()->getHeaders(),
            'navs'       => $this->getNavigation()->getNavs(),
        ]);
    }

    /**
     * Create a link for menu.
     *
     * @param string $label
     * @param array  $options
     */
    public function createLink($label, $options): ?string
    {
        if ($this->getNavigation()->isHided($options['link']))
        {
            return null;
        }

        if ($options['translate'] ?? false)
        {
            $label = $this->getTranslator()->trans($label, $options['params'] ?? [], $options['textDomain'] ?? 'navigation-app', $options['locale'] ?? null);
        }
        else
        {
            $label = $this->getTranslator()->mf($label, $options['params'] ?? [], $options['locale'] ?? null);
        }

        $attributes = $options['attributes'] ?? [];
        $blocked    = $this->getNavigation()->isBlocked($options['link']);

        if ( ! $blocked)
        {
            $label = $this->getAccesskeys()->create($label, $attributes);
        }

        if ($blocked)
        {
            unset($attributes['href']);
        }
        $attributes = $this->createAttributesString($attributes);

        return \appoencode(\sprintf('<%1$s %2$s>%4$s %3$s %5$s</%1$s>',
            ( ! $blocked ? 'a' : 'span'),
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
     */
    public function createHeader($label, $options): ?string
    {
        if ( ! $this->getNavigation()->headerHasNavs($label) && $options['hiddeEmpty'])
        {
            return null;
        }

        if ($options['translate'] ?? false)
        {
            $label = $this->getTranslator()->trans($label, $options['params'] ?? [], $options['textDomain'] ?? 'navigation-app', $options['locale'] ?? null);
        }
        else
        {
            $label = $this->getTranslator()->mf($label, $options['params'] ?? [], $options['locale'] ?? null);
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
     * @param string|null $link           Url to use in href atribute in links
     * @param string|null $template       You can change the template for your own if you need it at a specific time
     * @param string|null $scrollingStyle Options: All, Elastic, Jumping, Sliding. Default is Sliding
     */
    public function showPagination(Paginator $paginator, ?string $link = null, ?string $template = null, ?string $scrollingStyle = null, ?array $params = null): string
    {
        $scrollingStyle = $scrollingStyle ?: 'Sliding';

        $pages = \get_object_vars($paginator->getPages($scrollingStyle));

        //-- Add params for template
        if (null !== $params)
        {
            $pages = \array_merge($pages, (array) $params);
        }

        //-- Is a pagination for Jaxon-PHP
        if (0 === \strpos($link, 'JaxonLotgd.Ajax.Core.') || 0 === \strpos($link, 'JaxonLotgd.Ajax.Local.'))
        {
            $template = $template ?: 'parts/pagination-jaxon.twig';

            $pages['jaxon'] = $link;

            return $this->getTemplate()->renderTheme($template, $pages);
        }

        $template = $template ?: 'parts/pagination.twig';

        //-- Use request uri if not set link
        $link = $link ?: \LotgdRequest::getServer('REQUEST_URI');
        //-- Sanitize link / Delete previous queries of: "page", "c" and "commentPage"
        $link = \preg_replace('/(?:[?&]c=[[:digit:]]+)|(?:[?&]page=[[:digit:]]+)|(?:[?&]commentPage=[[:digit:]]+)/i', '', $link);

        if (false === \strpos($link, '?') && false !== \strpos($link, '&'))
        {
            $link = \preg_replace('/[&]/', '?', $link, 1);
        }

        //-- Check if have a ?
        if (false === \strpos($link, '?'))
        {
            $link = "{$link}?";
        }
        elseif (false !== \strpos($link, '?'))
        {
            $link = "{$link}&";
        }

        $pages['href'] = $link;

        return $this->getTemplate()->renderTheme($template, $pages);
    }
}
