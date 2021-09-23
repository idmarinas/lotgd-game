<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Character;

class Stats
{
    /**
     * Last category used.
     *
     * @var string
     */
    protected $lastCat = 'Other Info';

    /**
     * Array stats of character.
     *
     * @var array
     */
    protected $stats = [];

    /**
     * Add a attribute and/or value to the character stats display.
     *
     * @param string $label The label to use
     * @param string $value (optional) value to display
     */
    public function addcharstat(string $label, $value = null)
    {
        if (null !== $value)
        {
            if ( $this->getLastCat() === '' || $this->getLastCat() === '0')
            {
                $this->addcharstat('Other Info');
            }

            $this->stats[$this->getLastCat()][$label] = $value;
        }
        else
        {
            if ( ! isset($this->stats[$label]))
            {
                $this->stats[$label] = [];
            }
            $this->setLastCat($label);
        }
    }

    /**
     * Sets a value to the passed category & label for character stats.
     *
     * @param string $cat   The category for the char stat
     * @param string $label (Optional) The label associated with the value
     * @param string $val   The value of the attribute
     */
    public function setcharstat($cat, $label = null, $val)
    {
        if ( ! $label)
        {
            $this->stats[$cat] = $val;
        }
        elseif ( ! isset($this->stats[$cat][$label]))
        {
            $oldlabel = $this->getLastCat();
            $this->addcharstat($cat);
            $this->addcharstat($label, $val);
            $this->setLastCat($oldlabel);
        }
        else
        {
            $this->stats[$cat][$label] = $val;
        }
    }

    /**
     * Returns the value associated with the section & label.  Returns an empty string if the stat isn't set.
     *
     * @param string $section The character stat section
     * @param string $title   (Optional) The stat display label
     *
     * @return mixed The value associated with the stat
     */
    public function getcharstat($section, $title = null)
    {
        if (isset($this->stats[$section][$title]))
        {
            return $this->stats[$section][$title];
        }
        elseif (isset($this->stats[$section]))
        {
            return $this->stats[$section];
        }
    }

    /**
     * Resets the character stats array.
     */
    public function wipeStats()
    {
        $this->lastCat = 'Other Info';
        $this->stats   = [];

        return true;
    }

    /**
     * Get last category used.
     */
    public function getLastCat(): string
    {
        return $this->lastCat;
    }

    /**
     * Set last category used.
     *
     * @param string $lastCat Last category used
     *
     * @return self
     */
    public function setLastCat(string $lastCat)
    {
        $this->lastCat = $lastCat;

        return $this;
    }

    /**
     * Get array stats of character.
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * Set array stats of character. (THIS ACTION REMPLACE ALL STATS)
     * Use for alter original stats (getStats()).
     *
     * @return array
     */
    public function setStats(array $stats)
    {
        $this->stats = $stats;

        return $this;
    }
}
