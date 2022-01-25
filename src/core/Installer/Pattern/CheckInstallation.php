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

trait CheckInstallation
{
    use FormaterTrait;

    /**
     * Check if can install this version of game.
     */
    public function checkInstallation(int $fromVersion, int $toVersion): bool
    {
        $return = true;

        //-- Check if from and to version are a valid versions.
        if ( ! $this->isValidVersion($fromVersion) || ! $this->isValidVersion($toVersion))
        {
            $this->style->error($this->translator->trans('installer.check.installation.version.invalid', [
                'from' => $this->formatVersion($fromVersion),
                'to'   => $this->formatVersion($toVersion),
            ], parent::TRANSLATOR_DOMAIN));

            $return = false;
        }
        //-- Can't install it: is the same version
        elseif ($fromVersion === $toVersion)
        {
            $this->style->error($this->translator->trans('installer.check.installation.version.same', [], parent::TRANSLATOR_DOMAIN));

            $return = false;
        }
        //-- Seriously, install an inferior version on the superior version?
        elseif ($fromVersion > $toVersion)
        {
            $this->style->warning($this->translator->trans('installer.check.installation.version.superior', [], parent::TRANSLATOR_DOMAIN));

            $return = false;
        }
        //-- This new system can only upgrade from version 4.12.0 IDMarinas Edition
        //-- Allow a new installation
        elseif ($fromVersion < 41200 && $fromVersion > 0)
        {
            $this->style->error($this->translator->trans('installer.check.installation.version.less.info', [], parent::TRANSLATOR_DOMAIN));
            $this->style->error($this->translator->trans('installer.check.installation.version.less.upgrade', [], parent::TRANSLATOR_DOMAIN));

            $return = false;
        }

        return $return;
    }
}
