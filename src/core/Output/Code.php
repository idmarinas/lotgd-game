<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Output;

class Code
{
    /**
     * Array with codes format code => html.
     *
     * @var array
     */
    protected $codes = [
        'b' => 'b',
        'B' => 'strong',
        'i' => 'em',
    ];

    /**
     * Array with codes format code => html.
     *
     * @var array
     */
    protected $codesSpecialOpen = [
        '`c' => '<div class="text-center">',
        '`H' => '<span class="navhi">',
    ];

    /**
     * Array with codes format code => html.
     *
     * @var array
     */
    protected $codesSpecialClose = [
        '´c' => '</div>',
        '´H' => '</span>',
        '`n' => '<br>',
        '`0' => '</span>',
        '´0' => '</span>', //-- Prevent errors
    ];

    /**
     * Remplace original game codes.
     *
     * @return $this
     */
    public function setCodes(array $codes)
    {
        $this->codes = $codes;

        return $this;
    }

    /**
     * Add more codes to game.
     *
     * @return $this
     */
    public function addCodes(array $codes)
    {
        $this->codes = \array_merge($this->codes, $codes);

        return $this;
    }

    /**
     * Get array of codes.
     */
    public function getCodes(): array
    {
        return $this->codes;
    }

    /**
     * Remplace original game special codes.
     *
     * @return $this
     */
    public function setCodesSpecialOpen(array $codes)
    {
        $this->codesSpecialOpen = $codes;

        return $this;
    }

    /**
     * Add more special codes to game.
     *
     * @return $this
     */
    public function addCodesSpecialOpen(array $codes)
    {
        $this->codesSpecialOpen = \array_merge($this->codesSpecialOpen, $codes);

        return $this;
    }

    /**
     * Get array of codes.
     */
    public function getCodesSpecialOpen(): array
    {
        return $this->codesSpecialOpen;
    }

    /**
     * Remplace original game special codes.
     *
     * @return $this
     */
    public function setCodesSpecialClose(array $codes)
    {
        $this->codesSpecialClose = $codes;

        return $this;
    }

    /**
     * Add more special codes to game.
     *
     * @return $this
     */
    public function addCodesSpecialClose(array $codes)
    {
        $this->codesSpecialClose = \array_merge($this->codesSpecialClose, $codes);

        return $this;
    }

    /**
     * Get array of codes.
     */
    public function getCodesSpecialClose(): array
    {
        return $this->codesSpecialClose;
    }
}
