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

namespace Lotgd\Core\Twig\Extension\Pattern;

use Laminas\Paginator\Paginator;
use Twig\Environment;

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
    public function display(Environment $env)
    {
        return $env->load('_blocks/_partials.html.twig')->renderBlock('navigation_menu', [
            'navigation' => $this->navigation->getNavigation(),
            'headers'    => $this->navigation->getHeaders(),
            'navs'       => $this->navigation->getNavs(),
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
        if ($this->navigation->isHided($options['link']))
        {
            return null;
        }

        if ($options['translate'] ?? false)
        {
            $label = $this->translator->trans($label, $options['params'] ?? [], $options['textDomain'] ?? 'navigation_app', $options['locale'] ?? null);
        }
        else
        {
            $label = $this->format->messageFormatter($label, $options['params'] ?? [], $options['locale'] ?? null);
        }

        $attributes = $options['attributes'] ?? [];
        $blocked    = $this->navigation->isBlocked($options['link']);

        if ( ! $blocked)
        {
            $label = $this->accessKeys->create($label, $attributes);
        }

        if ($blocked)
        {
            unset($attributes['href']);
        }
        $attributes = $this->createAttributesString($attributes);

        return $this->format->colorize(sprintf(
            '<%1$s %2$s>%4$s %3$s %5$s</%1$s>',
            ($blocked ? 'span' : 'a'),
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
        if ( ! $this->navigation->headerHasNavs($label) && $options['hiddeEmpty'])
        {
            return null;
        }

        if ($options['translate'] ?? false)
        {
            $label = $this->translator->trans($label, $options['params'] ?? [], $options['textDomain'] ?? 'navigation_app', $options['locale'] ?? null);
        }
        else
        {
            $label = $this->format->messageFormatter($label, $options['params'] ?? [], $options['locale'] ?? null);
        }

        $attributes = $options['attributes'] ?? [];
        $attributes = $this->createAttributesString($attributes);

        return sprintf(
            '<%1$s %2$s>%3$s</%1$s>',
            (string) ($options['tag'] ?? 'span'),
            $attributes,
            $this->format->colorize($label, true)
        );
    }

    /**
     * Show pagination for a instance of Paginator.
     *
     * @param string|null       $link           Url to use in href atribute in links
     * @param string|array|null $template       You can change the template for your own if you need it at a specific time
     *                                          For render a block use array ['block_name', 'path/to/template']
     * @param string|null       $scrollingStyle Options: All, Elastic, Jumping, Sliding. Default is Sliding
     */
    public function showPagination(Environment $env, Paginator $paginator, ?string $link = null, $template = null, ?string $scrollingStyle = null, ?array $params = null): string
    {
        $scrollingStyle = $scrollingStyle ?: 'Sliding';

        $pages = get_object_vars($paginator->getPages($scrollingStyle));

        //-- Add params for template
        if (null !== $params)
        {
            $pages = array_merge($pages, $params);
        }

        //-- Is a pagination for Stimulus controller
        /*
         * Format for Pagination Stimulus controller:
         *      stimulus:ControllerName:Url
         */
        if (0 === strpos($link, 'stimulus:'))
        {
            $template = $template ?: ['pagination_stimulus', '_blocks/_partials.html.twig'];

            $stimulus = explode(':', $link);

            $pages['stimulus_controller'] = $stimulus[1];
            $pages['stimulus_url']        = $stimulus[2];

            return $this->renderPagination($env, $template, $pages);
        }

        $template = $template ?: ['pagination', '_blocks/_partials.html.twig'];

        //-- Use request uri if not set link
        $link = $link ?: $this->request->getServer('REQUEST_URI');
        //-- Sanitize link / Delete previous queries of: "page", "c" and "commentPage"
        $link = preg_replace('/(?:[?&]c=[[:digit:]]+)|(?:[?&]page=[[:digit:]]+)|(?:[?&]commentPage=[[:digit:]]+)/i', '', $link);

        if (false === strpos($link, '?') && false !== strpos($link, '&'))
        {
            $link = preg_replace('/[&]/', '?', $link, 1);
        }

        //-- Check if have a ?
        if (false === strpos($link, '?'))
        {
            $link = "{$link}?";
        }
        elseif (false !== strpos($link, '?'))
        {
            $link = "{$link}&";
        }

        $pages['href'] = $link;

        return $this->renderPagination($env, $template, $pages);
    }

    /**
     * Add a link, but not nav.
     *
     * @param string $string
     */
    public function lotgdUrl(string $link): string
    {
        $this->navigation->addNavAllow($link);

        return $link;
    }

    /**
     * Render template for pagination.
     *
     * @param string|array|null $template
     * @param array             $pages
     */
    protected function renderPagination(Environment $env, $template, $pages): string
    {
        //-- Render block template
        if (\is_array($template))
        {
            return $env->load($template[1])->renderBlock($template[0], $pages);
        }

        return $env->render($template, $pages);
    }
}
