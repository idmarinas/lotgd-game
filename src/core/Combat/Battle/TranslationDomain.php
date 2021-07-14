<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

trait TranslationDomain
{
    public $translationDomain = 'page_battle';

    /**
     * Get translator domain for battle.
     */
    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    /**
     * Set translator domain for battle.
     */
    public function setTranslationDomain(string $translationDomain): self
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }
}
