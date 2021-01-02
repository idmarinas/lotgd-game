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

namespace Lotgd\Core\Service;

use Lotgd\Core\Output\Censor as OutputCensor;

class Censor
{
    public const LOTGD_DICTIONARY_PATH = 'data/dictionary';

    public function __construct()
    {
        $language  = \Locale::getDefault();
        $profanity = new OutputCensor();
        $profanity->addDictionary(self::LOTGD_DICTIONARY_PATH.'/en.php'); //-- Custom dictionary

        if ('en' != $language)
        {
            $profanity->addDictionary($language);
            $customLanguage = self::LOTGD_DICTIONARY_PATH."/{$language}.php";

            if (\is_file($customLanguage))
            {
                $profanity->addDictionary($customLanguage);
            }
        }

        return $profanity;
    }
}
