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

@trigger_error(ThemeList::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
trait ThemeList
{
    use Cache;
    use Container;

    /**
     * Get list array.
     */
    public function getThemeList(): array
    {
        $cacheKey = 'lotgd-core-pattern-theme-list';
        $cache    = $this->getCacheApp();

        return $cache->getItem($cacheKey, function ()
        {
            //-- This is a perma cache, for update need clear cache

            // A generic way of allowing a theme to be selected.
            $handle = @\opendir('themes');

            // Template directory open failed
            if ( ! $handle)
            {
                return [];
            }

            $skins = [];

            while (false !== ($file = @\readdir($handle)))
            {
                if ('html' == \pathinfo($file, PATHINFO_EXTENSION))
                {
                    $skins[$file] = \str_replace(['-', '_'], ' ', \ucfirst(\substr($file, 0, \strpos($file, '.htm'))));
                }
            }

            return $skins;
        });
    }
}
