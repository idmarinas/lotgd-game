<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.0.0
 */

namespace Lotgd\Core\Installer\Pattern;

trait FormaterTrait
{
    public function formatVersion($version)
    {
        $version = (string) $version;

        $path    = (int) \substr($version, -2);
        $version = \substr_replace($version, '', -2);
        $minor   = (int) \substr($version, -2);
        $version = \substr_replace($version, '', -2);
        $major   = (int) \substr($version, -2);

        return "{$major}.{$minor}.{$path}";
    }
}
