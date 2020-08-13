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

namespace Lotgd\Core\Output;

use Snipe\BanBuilder\CensorWords;

class Censor extends CensorWords
{
    /**
     * Last original text, before filter.
     *
     * @var string
     */
    protected $lotgdOrigString = '';

    /**
     * Last matched words, after filter.
     *
     * @var array
     */
    protected $lotgdMatchWords = [];

    /**
     * Apply profanity filter.
     *
     * @see Snipe\BanBuilder\CensorWords
     *
     * @param mixed $fullWords
     */
    public function filter(string $string, $fullWords = false): string
    {
        $censor = $this->censorString($string, $fullWords);

        $this->lotgdOrigString = $censor['orig'];
        $this->lotgdMatchWords = $censor['matched'];

        return $censor['clean'];
    }

    /**
     * Get the original text.
     */
    public function getOrigString(): string
    {
        return $this->lotgdOrigString;
    }

    /**
     * Get matched words.
     */
    public function getMatchWords(): array
    {
        return $this->lotgdMatchWords;
    }
}
