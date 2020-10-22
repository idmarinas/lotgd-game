<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 3.0.0
 */

namespace Lotgd\Core\Template;

class Theme extends Template
{
    /**
     * Render a theme
     * Used in pageparts.php for render a page.
     *
     * @param mixed $context
     */
    public function renderThemeOld($context)
    {
        \trigger_error(\sprintf(
            'Class %s (before renderTheme) is deprecated in 4.5.0 and deleted in 5.0.0. Use new system for render pages',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->render($this->getTheme(), (array) $context);
    }

    /**
     * Renders a template of the theme.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    public function renderThemeTemplate($name, $context)
    {
        global $html, $session;

        \trigger_error(\sprintf(
            'Class %s is deprecated in 4.5.0 and deleted in 5.0.0, please use "renderTheme($template, $context)" instead',
            __METHOD__
        ), E_USER_DEPRECATED);

        $userPre = $html['userPre'] ?? [];
        $user    = $session['user'] ?? [];
        unset($user['password']);

        $context = \array_merge([
            'userPre' => $userPre,
            'user'    => $user, //-- Actual user data for this template
            'session' => $html['session'] ?? [], //-- Session data declared in page_header or popup_header
        ],
        $context);

        return $this->render("{$this->themefolder}/{$name}", (array) $context);
    }

    /**
     * Renders a template of module.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     *
     * @deprecated 4.5.0 deleted in 5.0.0
     */
    public function renderModuleTemplate($name, $context)
    {
        global $html, $session;

        \trigger_error(\sprintf(
            'Class %s is deprecated in 4.5.0 and deleted in 5.0.0, please use namespace "@module/path/to/template.html.twig" instead',
            __METHOD__
        ), E_USER_DEPRECATED);

        $userPre = $html['userPre'] ?? [];
        $user    = $session['user'] ?? [];
        unset($user['password']);

        $context = \array_merge([
            'userPre' => $userPre,
            'user'    => $user, //-- Actual user data for this template
            'session' => $html['session'] ?? [], //-- Actual session data for this template
        ],
        $context);

        return $this->render("module/{$name}", (array) $context);
    }

    /**
     * Renders a template of LOTGD.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     *
     * @deprecated 4.5.0 deleted in 5.0.0
     */
    public function renderLotgdTemplate($name, $context)
    {
        \trigger_error(\sprintf(
            'Class %s is deprecated (is only an alias) in 4.5.0 and deleted in 5.0.0, please use "render($name, $context)" instead',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->render($name, (array) $context);
    }
}
