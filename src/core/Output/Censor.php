<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Output;

use Throwable;
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

    public function __construct(string $locale, string $projectDir)
    {
        parent::__construct();

        try
        {
            //-- Try add locale dictionary
            $this->addDictionary($locale);
        }
        catch (Throwable $th)
        {
            //-- If fail add en-base dictionary
            $this->addDictionary('en-base');
        }

        //-- Add custom local dictionary
        $dictionary = "{$projectDir}/data/dictionary/{$locale}.php";

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
