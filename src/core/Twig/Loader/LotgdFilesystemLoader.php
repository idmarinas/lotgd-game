<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Twig\Loader;

use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig\Loader\SourceContextLoaderInterface;

class LotgdFilesystemLoader extends FilesystemLoader implements LoaderInterface, SourceContextLoaderInterface
{
    protected $themeNamespace;
    private $decoratedLoader;

    public function __construct(FilesystemLoader $decoratedLoader)
    {
        $this->decoratedLoader = $decoratedLoader;
    }

    /**
     * Set namespace of active theme.
     */
    public function setThemeNamespace(string $namespace)
    {
        $this->themeNamespace = $namespace;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null
     */
    protected function findTemplate($template, $throw = true)
    {
        $template = \str_replace('{theme}', "@theme{$this->themeNamespace}", $template);

        $tpl = $this->decoratedLoader->findTemplate($template, false);

        //-- Fallback theme namespace to default namespace
        if ( ! $tpl && false !== \strpos($template, '@theme'))
        {
            $pos         = \strpos($template, '/');
            $newTemplate = \substr($template, $pos + 1);

            $tpl = $this->decoratedLoader->findTemplate($newTemplate, $throw);

            if ($tpl)
            {
                $this->decoratedLoader->cache[$template] = $tpl; //-- Cache original them namespace
            }
        }

        return $tpl;
    }
}
