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

use Lotgd\Core\Template\Theme as Environment;

trait CoreFunction
{
    /**
     * Add server name to url query.
     */
    public function baseUrl(string $query): string
    {
        return \sprintf('//%s/%s', \LotgdRequest::getServer('SERVER_NAME'), $query);
    }

    /**
     * Translate a title of page.
     *
     * @deprecated 4.5.0 deleted in version 5.0.0
     *
     * @return string
     */
    public function pageTitle(array $title)
    {
        \trigger_error(\sprintf(
            'Usage of %s (page_title() Twig function) is obsolete since 4.5.0; and delete in version 5.0.0, use "head_title()" instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $title = \LotgdTranslator::t($title['title'], $title['params'], $title['textDomain']);

        return \LotgdSanitize::fullSanitize($title);
    }

    /**
     * Get version of game.
     */
    public function gameVersion(): string
    {
        return \Lotgd\Core\Application::VERSION;
    }

    /**
     * Get copyright of game.
     */
    public function gameCopyright(): string
    {
        return \Lotgd\Core\Application::LICENSE.\Lotgd\Core\Application::COPYRIGHT;
    }

    /**
     * Get value of setting.
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function getsetting($name, $default): ?string
    {
        return $this->getLotgdSettings()->getSetting($name, $default);
    }

    /**
     * Activate a hook.
     *
     * @param string $name
     * @param array  $data
     *
     * @return mixed
     */
    public function triggerEvent($name, $data = [])
    {
        $hook = $this->getHookManager();

        $hook->trigger($name, null, $data);

        return modulehook($name, $data);
    }

    /**
     * Validating a protocol.
     *
     * @param string $protocol
     * @param mixed  $url
     *
     * @return bool
     */
    public function isValidProtocol($url)
    {
        // We should check all legeal protocols
        $protocols = ['http', 'https', 'ftp', 'ftps'];
        $protocol  = \explode(':', $url, 2);
        $protocol  = $protocol[0];

        // This will take care of download strings such as: not publically released or contact admin
        return \in_array($protocol, $protocols);
    }

    /**
     * Time in the game.
     *
     * @return string
     */
    public function gametime()
    {
        return getgametime();
    }

    /**
     * Seconds to next game day.
     *
     * @return int
     */
    public function secondstonextgameday()
    {
        return secondstonextgameday();
    }

    /**
     * Render a PvP table list.
     */
    public function pvpListTable(array $params): string
    {
        $params['linkBase']  = ($params['linkBase'] ?? 'pvp.php') ?: 'pvp.php';
        $params['linkExtra'] = ($params['linkExtra'] ?? '?act=attack') ?: '?act=attack';

        $params['linkAttack'] = "{$params['linkBase']}{$params['linkExtra']}";
        $params['linkAttack'] .= ($params['isInn'] ?? false) ? '&inn=1' : '';

        return $this->getTheme()->renderBlock('pvp_list', '{theme}/_blocks/_pvp.html.twig', $params);
    }

    /**
     * Render a count of sleepers for zone.
     */
    public function pvpListSleepers(array $params): string
    {
        return $this->getTheme()->renderBlock('pvp_sleepers', '{theme}/_blocks/_pvp.html.twig', $params);
    }

    /**
     * Get cookie name.
     */
    public function sessionCookieName(): string
    {
        $config = $this->getContainer('GameConfig');

        return $config['session_config']['name'] ?? 'PHPSESSID';
    }

    /**
     * Function to use bdump of Tracy debugger.
     *
     * @param mixed  $param
     * @param string $name
     */
    public function bdump($param, $name = null): void
    {
        bdump($param, $name);
    }

    /**
     * Dump var and return a string.
     *
     * @param mixed $var
     */
    public function varDump($var): string
    {
        return '<pre>'.\var_export($var, true).'</pre>';
    }

    /**
     * Renders a module template.
     *
     * @param array        $context
     * @param string|array $template      The template to render or an array of templates to try consecutively
     * @param array        $variables     The variables to pass to the template
     * @param bool         $withContext
     * @param bool         $ignoreMissing Whether to ignore missing templates or not
     * @param bool         $sandboxed     Whether to sandbox the template or not
     *
     * @return string The rendered template
     */
    public function includeModuleTemplate(Environment $env, $context, $template, $variables = [], $withContext = true, $ignoreMissing = false, $sandboxed = false)
    {
        $include = $env->getFunction('include')->getCallable();

        return $include($env, $context, "module/{$template}", $variables, $withContext, $ignoreMissing, $sandboxed);
    }

    /**
     * Renders a theme template.
     *
     * @param array        $context
     * @param string|array $template      The template to render or an array of templates to try consecutively
     * @param array        $variables     The variables to pass to the template
     * @param bool         $withContext
     * @param bool         $ignoreMissing Whether to ignore missing templates or not
     * @param bool         $sandboxed     Whether to sandbox the template or not
     *
     * @return string The rendered template
     */
    public function includeThemeTemplate(Environment $env, $context, $template, $variables = [], $withContext = true, $ignoreMissing = false, $sandboxed = false)
    {
        $include = $env->getFunction('include')->getCallable();

        return $include($env, $context, "{$env->getThemefolder()}/{$template}", $variables, $withContext, $ignoreMissing, $sandboxed);
    }

    /**
     * Render new layout system.
     *
     * @param array        $context
     * @param string|array $template      The template to render or an array of templates to try consecutively
     * @param array        $variables     The variables to pass to the template
     * @param bool         $withContext
     * @param bool         $ignoreMissing Whether to ignore missing templates or not
     * @param bool         $sandboxed     Whether to sandbox the template or not
     *
     * @return string The rendered template
     */
    public function includeLayoutTemplate(Environment $env, $context, $template, $variables = [], $withContext = true, $ignoreMissing = false, $sandboxed = false)
    {
        $include = $env->getFunction('include')->getCallable();

        return $include($env, $context, "@theme{$env->getThemeNamespace()}/{$template}", $variables, $withContext, $ignoreMissing, $sandboxed);
    }
}
