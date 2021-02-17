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

namespace Lotgd\Core\Tool;

use Snipe\BanBuilder\CensorWords;

class Censor extends CensorWords
{
    public const LOTGD_DICTIONARY_PATH = 'data/dictionary';

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
