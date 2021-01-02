<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.10.0
 */

namespace Lotgd\Core\Installer\Upgrade\Version_41000;

use Lotgd\Core\Installer\UpgradeAbstract;
use Symfony\Component\Filesystem\Filesystem;
use Tracy\Debugger;

class Upgrade extends UpgradeAbstract
{
    public const VERSION_NUMBER    = 41000;
    public const CONFIG_DIR_GLOBAL = 'config/autoload/global';

    /**
     * Step 1: Remove some files.
     */
    public function step1(): bool
    {
        try
        {
            $files = [
                self::CONFIG_DIR_GLOBAL.'/delegators-lotgd-core.php',
                self::CONFIG_DIR_GLOBAL.'/form-lotgd-core.php',
                self::CONFIG_DIR_GLOBAL.'/hydrators-lotgd-core.php',
                self::CONFIG_DIR_GLOBAL.'/initializers-lotgd-core.php',
                self::CONFIG_DIR_GLOBAL.'/input-filter-lotgd-core.php',
                self::CONFIG_DIR_GLOBAL.'/invokables-lotgd-core.php',
                self::CONFIG_DIR_GLOBAL.'/jaxon-lotgd-core.php',
                self::CONFIG_DIR_GLOBAL.'/services-lotgd-core.php',
                self::CONFIG_DIR_GLOBAL.'/session-lotgd-core.php',
                self::CONFIG_DIR_GLOBAL.'/shared-lotgd-core.php',
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
