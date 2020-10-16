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

use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;

class LotgdFilesystemLoader extends FilesystemLoader
{
    /**
     * {@inheritdoc}
     *
     * @return string|null
     */
    protected function findTemplate($template, $throw = true)
    {
        $tpl = parent::findTemplate($template, false);

        //-- Fallback theme namespace to default namespace
        if ( ! $tpl && false !== \strpos($template, '@theme'))
        {
            $pos         = \strpos($template, '/');
            $newTemplate = \substr($template, $pos + 1);

            $tpl = parent::findTemplate($newTemplate, $throw);

            if ($tpl)
            {
                $this->cache[$template] = $tpl; //-- Cache original them namespace
            }
        }

        if ( ! $tpl)
        {
            throw new LoaderError($this->errorCache[$template]);
        }

        return $tpl;
    }
}
