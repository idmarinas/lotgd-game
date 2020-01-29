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

namespace Lotgd\Core\Installer\Upgrade\Version_40100;

use Lotgd\Core\Installer\UpgradeAbstract;
use Tracy\Debugger;

class Upgrade extends UpgradeAbstract
{
    const VERSION_NUMBER = 40100;

    /**
     * Step 1: Update settings.
     *
     * @return bool
     */
    public function step1(): bool
    {
        try
        {
            $this->messages[] = \LotgdTranslator::t('upgrade.version.to', ['version' => $this->getNameVersion(self::VERSION_NUMBER)], self::TRANSLATOR_DOMAIN);

            $settings = $this->doctrine->getRepository('LotgdCore:Settings');

            //-- Update languages to new format
            // This does not delete the configured languages, but adapts to the new format
            $languagesOri = $settings->findOneBy(['setting' => 'serverlanguages']);
            $languages = $languagesOri->getValue();

            $languages = explode(',', $languages);

            $lang = [];
            $count = count($languages);
            for ($i = 0; $i < $count; $i++)
            {
                $lang[] = $languages[$i];//-- Only need code
                $i++; //-- Avoid name of language
            }

            $languagesOri->setValue(implode(',', $lang));

            $this->doctrine->persist($languagesOri);

            $this->messages[] = \LotgdTranslator::t('upgrade.version.data', ['name' => 'settings -> serverlanguages'], self::TRANSLATOR_DOMAIN);

            //-- Update petition types to new format (Deleted old types)
            $petitions = new \Lotgd\Core\Entity\Settings();
            $petitions->setSetting('petition_types')
                ->setValue('petition.types.general,petition.types.report.bug,petition.types.suggestion,petition.types.commentpetition.types.other')
            ;

            $this->doctrine->persist($petitions);

            //-- Process data
            $this->doctrine->flush();

            $this->messages[] = \LotgdTranslator::t('upgrade.version.data', ['name' => 'settings -> petition_types'], self::TRANSLATOR_DOMAIN);

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }
}
