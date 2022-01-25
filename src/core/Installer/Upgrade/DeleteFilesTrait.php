<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.4.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Throwable;
use Symfony\Component\Filesystem\Filesystem;

trait DeleteFilesTrait
{
    public function removeFiles(array $files): bool
    {
        $fs = new Filesystem();

        try
        {
            $fs->remove($files);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }
}
