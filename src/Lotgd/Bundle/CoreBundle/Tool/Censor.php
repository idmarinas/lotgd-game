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

namespace Lotgd\Bundle\CoreBundle\Tool;

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

        try
        {
            //-- Try add local dictionary
            $this->addDictionary($locale);
        }
        catch (\Throwable $th)
        {
            //-- If fail add en-base dictionary
            $this->addDictionary('en-base');
        }

        $dictionary = dirname(__DIR__, 2)."/data/dictionary/{$locale}.php";

        if (\is_file($dictionary))
        {
            $this->addDictionary($dictionary);
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
