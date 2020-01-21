<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Template\Theme as ThemeCore;

trait ThemeList
{
    use Cache;
    use Container;

    /**
     * Get list array.
     *
     * @return array
     */
    public function getThemeList(): array
    {
        $cacheKey = str_replace('\\', '-', ThemeList::class);
        $cache = $this->getCache();

        $skins = $cache->getItem($cacheKey);
        if (! is_array($skins))
        {

            // A generic way of allowing a theme to be selected.
            $handle = @opendir('data/template');

            // Template directory open failed
            if (! $handle)
            {
                return [];
            }

            $skins = [];
            while (false !== ($file = @readdir($handle)))
            {
                if ('html' == pathinfo($file, PATHINFO_EXTENSION))
                {
                    $skins[$file] = str_replace(['-', '_'], ' ', ucfirst(substr($file, 0, strpos($file, '.htm'))));
                }
            }

            $cache->setItem($cacheKey, $skins);
        }

        return $skins;
    }
}
