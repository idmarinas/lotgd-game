<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Translator\Translator as TranslatorCore;
use Symfony\Component\Translation\Translator as TranslationTranslator;

trait Translator
{
    protected $lotgdTranslator;
    protected $lotgdSymfonyTranslator;

    /**
     * Get translator instance.
     *
     * @return object|null
     */
    public function getTranslator()
    {
        if ( ! $this->lotgdTranslator instanceof TranslatorCore)
        {
            $this->lotgdTranslator = $this->getContainer(TranslatorCore::class);
        }

        return $this->lotgdTranslator;
    }

    /**
     * Symfony Translator instance.
     */
    public function symfonyTranslator(): TranslationTranslator
    {
        if ( ! $this->lotgdSymfonyTranslator instanceof TranslationTranslator)
        {
            $this->lotgdSymfonyTranslator = \LotgdKernel::get('translator');
        }

        return $this->lotgdSymfonyTranslator;
    }
}
