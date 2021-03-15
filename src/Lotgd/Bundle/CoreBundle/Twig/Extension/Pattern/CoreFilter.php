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

namespace Lotgd\Bundle\CoreBundle\Twig\Extension\Pattern;

trait CoreFilter
{
    /**
     * Show an affirmation or negation.
     *
     * @param int|bool $value      Value to check
     * @param string   $yes        Translation key
     * @param string   $no         Translation key
     * @param string   $textDomain Domain for translation
     *
     * @return text
     */
    public function affirmationNegation($value, $yes = 'app.adverb.yes', $no = 'app.adverb.no', $textDomain = 'lotgd_core_default')
    {
        $value = (int) $value;

        $text = 0 == $value ? $no : $yes;

        return $this->translator->trans($text, [], $textDomain);
    }
}
