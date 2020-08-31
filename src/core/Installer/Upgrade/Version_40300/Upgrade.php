<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\Installer\Upgrade\Version_40300;

use Lotgd\Core\Component\Filesystem;
use Lotgd\Core\Installer\UpgradeAbstract;
use Tracy\Debugger;

class Upgrade extends UpgradeAbstract
{
    const VERSION_NUMBER = 40300;
    const CONFIG_DIR_GLOBAL = 'config/autoload/global';

    /**
     * Step 1: Deleted some files that are not needed.
     */
    public function step1(): bool
    {
        try
        {
            $file = new Filesystem();

            //-- Remove aliases file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/aliases.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/aliases.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/aliases.php'], self::TRANSLATOR_DOMAIN);
            }

            //-- Remove factories file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/factories.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/factories.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/factories.php'], self::TRANSLATOR_DOMAIN);
            }

            //-- Remove session file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/session.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/session.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/session.php'], self::TRANSLATOR_DOMAIN);
            }

            //-- Remove validator-lotgd file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/validator-lotgd.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/validator-lotgd.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/validator-lotgd.php'], self::TRANSLATOR_DOMAIN);
            }

            //-- Remove doctrine file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/doctrine.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/doctrine.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/doctrine.php'], self::TRANSLATOR_DOMAIN);
            }

            //-- Remove initializers file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/initializers.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/initializers.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/initializers.php'], self::TRANSLATOR_DOMAIN);
            }

            //-- Remove invokables file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/invokables.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/invokables.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/invokables.php'], self::TRANSLATOR_DOMAIN);
            }

            //-- Remove services file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/services.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/services.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/services.php'], self::TRANSLATOR_DOMAIN);
            }

            //-- Remove lazy_services file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/lazy_services.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/lazy_services.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/lazy_services.php'], self::TRANSLATOR_DOMAIN);
            }

            //-- Remove shared file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/shared.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/shared.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/shared.php'], self::TRANSLATOR_DOMAIN);
            }

            //-- Remove twig file
            if ($file->exists(self::CONFIG_DIR_GLOBAL.'/twig.php'))
            {
                $file->remove(self::CONFIG_DIR_GLOBAL.'/twig.php');
                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => self::CONFIG_DIR_GLOBAL.'/twig.php'], self::TRANSLATOR_DOMAIN);
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
