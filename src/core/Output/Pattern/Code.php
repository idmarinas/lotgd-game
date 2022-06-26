<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Output\Pattern;

trait Code
{
    protected $codePatternOpen;
    protected $codePatternClose;
    protected $codeReplacementOpen;
    protected $codeReplacementClose;
    protected $codeSpecialOpen;
    protected $codeSpecialClose;
    protected $codeSpecialPatternOpen;
    protected $codeSpecialPatternClose;
    protected $codeSpecialReplacementOpen;
    protected $codeSpecialReplacementClose;

    /**
     * Get the complete color array.
     *
     * @return array
     */
    public function getCodes()
    {
        return $this->codes->getCodes();
    }

    /**
     * Get patterns for open code tag.
     *
     * @return array
     */
    public function getCodePatternOpen()
    {
        if ( ! $this->codePatternOpen)
        {
            $this->codePatternOpen = \array_map(fn($k) => "`{$k}", \array_keys($this->getCodes()));
        }

        return $this->codePatternOpen;
    }

    /**
     * Get patterns for close code tag.
     *
     * @return array
     */
    public function getCodePatternClose()
    {
        if ( ! $this->codePatternClose)
        {
            $this->codePatternClose = \array_map(fn($k) => "´{$k}", \array_keys($this->getCodes()));
        }

        return $this->codePatternClose;
    }

    /**
     * Get replacement  for code open code colors.
     */
    public function getCodeReplacementOpen()
    {
        if ( ! $this->codeReplacementOpen)
        {
            $this->codeReplacementOpen = \array_map(fn($k) => "<{$k}>", \array_values($this->getCodes()));
        }

        return $this->codeReplacementOpen;
    }

    /**
     * Get replacement for code close code colors.
     */
    public function getCodeReplacementClose()
    {
        if ( ! $this->codeReplacementClose)
        {
            $this->codeReplacementClose = \array_map(fn($k) => "</{$k}>", \array_values($this->getCodes()));
        }

        return $this->codeReplacementClose;
    }

    /**
     * Get Special  for code open code colors.
     */
    public function getCodeSpecialOpen()
    {
        if ( ! $this->codeSpecialOpen)
        {
            $this->codeSpecialOpen = $this->codes->getCodesSpecialOpen();
        }

        return $this->codeSpecialOpen;
    }

    /**
     * Get Specials for close code tag.
     *
     * @return array
     */
    public function getCodeSpecialClose()
    {
        if ( ! $this->codeSpecialClose)
        {
            $this->codeSpecialClose = $this->codes->getCodesSpecialClose();
        }

        return $this->codeSpecialClose;
    }

    /**
     * Get patterns for open code tag.
     *
     * @return array
     */
    public function getCodeSpecialPatternOpen()
    {
        if ( ! $this->codeSpecialPatternOpen)
        {
            $this->codeSpecialPatternOpen = \array_keys($this->getCodeSpecialOpen());
        }

        return $this->codeSpecialPatternOpen;
    }

    /**
     * Get patterns for close code tag.
     *
     * @return array
     */
    public function getCodeSpecialPatternClose()
    {
        if ( ! $this->codeSpecialPatternClose)
        {
            $this->codeSpecialPatternClose = \array_keys($this->getCodeSpecialClose());
        }

        return $this->codeSpecialPatternClose;
    }

    /**
     * Get replacement  for code open code colors.
     */
    public function getCodeSpecialReplacementOpen()
    {
        if ( ! $this->codeSpecialReplacementOpen)
        {
            $this->codeSpecialReplacementOpen = \array_values($this->getCodeSpecialOpen());
        }

        return $this->codeSpecialReplacementOpen;
    }

    /**
     * Get replacement for code close code colors.
     */
    public function getCodeSpecialReplacementClose()
    {
        if ( ! $this->codeSpecialReplacementClose)
        {
            $this->codeSpecialReplacementClose = \array_values($this->getCodeSpecialClose());
        }

        return $this->codeSpecialReplacementClose;
    }
}
