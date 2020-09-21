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

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\SandboxExtension;

trait CoreFunction
{
    /**
     * Add server name to url query.
     */
    public function baseUrl(string $query): string
    {
        return \sprintf('//%s/%s', \LotgdHttp::getServer('SERVER_NAME'), $query);
    }

    /**
     * Translate a title of page.
     *
     * @return string
     */
    public function pageTitle(array $title)
    {
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

        $data = $hook->prepareArgs($data);
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
        $protocol  = explode(':', $url, 2);
        $protocol  = $protocol[0];

        // This will take care of download strings such as: not publically released or contact admin
        return in_array($protocol, $protocols);
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

        return $this->getTheme()->renderThemeTemplate('parts/pvp-list.twig', $params);
    }

    /**
     * Render a count of sleepers for zone.
     */
    public function pvpListSleepers(array $params): string
    {
        return $this->getTheme()->renderThemeTemplate('parts/pvp-sleepers.twig', $params);
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
        return '<pre>'.var_export($var, true).'</pre>';
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
        $alreadySandboxed = false;
        $sandbox          = null;

        if ($withContext)
        {
            $variables = array_merge($context, $variables);
        }

        if ($isSandboxed = $sandboxed && $env->hasExtension(SandboxExtension::class))
        {
            $sandbox = $env->getExtension(SandboxExtension::class);

            if ( ! $alreadySandboxed = $sandbox->isSandboxed())
            {
                $sandbox->enableSandbox();
            }
        }

        try
        {
            return $env->resolveTemplate("module/{$template}")->render($variables);
        }
        catch (LoaderError $e)
        {
            if ( ! $ignoreMissing)
            {
                throw $e;
            }
        }
        finally
        {
            if ($isSandboxed && ! $alreadySandboxed)
            {
                $sandbox->disableSandbox();
            }
        }
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
        $alreadySandboxed = false;
        $sandbox          = null;

        if ($withContext)
        {
            $variables = array_merge($context, $variables);
        }

        if ($isSandboxed = $sandboxed && $env->hasExtension(SandboxExtension::class))
        {
            $sandbox = $env->getExtension(SandboxExtension::class);

            if ( ! $alreadySandboxed = $sandbox->isSandboxed())
            {
                $sandbox->enableSandbox();
            }
        }

        try
        {
            return $env->resolveTemplate("{$env->getThemefolder()}/{$template}")->render($variables);
        }
        catch (LoaderError $e)
        {
            if ( ! $ignoreMissing)
            {
                throw $e;
            }
        }
        finally
        {
            if ($isSandboxed && ! $alreadySandboxed)
            {
                $sandbox->disableSandbox();
            }
        }
    }
}
