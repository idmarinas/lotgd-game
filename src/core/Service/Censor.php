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

    public function __construct($locale)
    {
        parent::__construct();

        $this->addDictionary(self::LOTGD_DICTIONARY_PATH.'/en.php'); //-- Custom dictionary

        if ('en' != $locale)
        {
            $this->addDictionary($locale);
            $customLanguage = self::LOTGD_DICTIONARY_PATH."/{$locale}.php";

            if (\is_file($customLanguage))
            {
                $this->addDictionary($customLanguage);
            }
        }
    }
}
