<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Output\Pattern;

trait Color
{
    protected $colorPatternOpen;
    protected $colorPatternClose;
    protected $colorReplacement;

    /**
     * Get the complete color array.
     *
     * @return array
     */
    public function getColors()
    {
        return $this->colors->getColors();
    }

    /**
     * Get patterns for open color code.
     *
     * @return array
     */
    public function getColorPatternOpen()
    {
        if ( ! $this->colorPatternOpen)
        {
            $this->colorPatternOpen = \array_map(fn($k) => "`{$k}", \array_keys($this->getColors()));
        }

        return $this->colorPatternOpen;
    }

    /**
     * Get patterns for close color code.
     *
     * @return array
     */
    public function getColorPatternClose()
    {
        if ( ! $this->colorPatternClose)
        {
            $this->colorPatternClose = \array_map(fn($k) => "´{$k}", \array_keys($this->getColors()));
        }

        return $this->colorPatternClose;
    }

    /**
     * Get replacement for code open code colors.
     */
    public function getColorReplacementOpen()
    {
        if ( ! $this->colorReplacement)
        {
            $this->colorReplacement = \array_map(fn($k) => "<span class='{$k}'>", \array_values($this->getColors()));
        }

        return $this->colorReplacement;
    }
}
