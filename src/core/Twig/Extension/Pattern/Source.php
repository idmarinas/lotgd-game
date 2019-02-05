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

/**
 * Trait to created source link.
 */
trait Source
{
    /**
     * Get source link.
     *
     * @return string
     */
    function gameSource(): string
    {
        $sourcelink = 'source.php?url='.preg_replace('/[?].*/', '', (\LotgdHttp::getServer('REQUEST_URI')));

        return \LotgdTheme::renderThemeTemplate('parts/source.twig', [
            'sourceHref' => $sourcelink
        ]);
    }
}
