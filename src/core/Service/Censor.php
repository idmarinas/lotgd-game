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

class Censor extends OutputCensor
{
    public const LOTGD_DICTIONARY_PATH = 'data/dictionary';

    public function __construct()
    {
        parent::__construct();

        $language  = \Locale::getDefault();
        $this->addDictionary(self::LOTGD_DICTIONARY_PATH.'/en.php'); //-- Custom dictionary

        if ('en' != $language)
        {
            $this->addDictionary($language);
            $customLanguage = self::LOTGD_DICTIONARY_PATH."/{$language}.php";

            if (\is_file($customLanguage))
            {
                $this->addDictionary($customLanguage);
            }
        }
    }
}
