<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.9.0
 */

namespace Lotgd\Core\Installer\Upgrade\Version_40900;

use Lotgd\Core\Installer\UpgradeAbstract;
use Symfony\Component\Filesystem\Filesystem;
use Tracy\Debugger;

class Upgrade extends UpgradeAbstract
{
    public const VERSION_NUMBER    = 40900;
    public const CONFIG_DIR_GLOBAL = 'config/autoload/global';

    /**
     * Step 1: Remove some files.
     */
    public function step1(): bool
    {
        try
        {
            $files = [
                self::CONFIG_DIR_GLOBAL.'/doctrine-lotgd-core.php',
            ];

            try
            {
                $fs = new Filesystem();

                $fs->remove($files);

                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => '"'.\implode('", "', $files).'"'], self::TRANSLATOR_DOMAIN);
            }
            catch (\Throwable $th)
            {
                $this->messages[] = $th->getMessage();
            }

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }
}
