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

        // \Zend\Debug\Debug::dump($label);
        if (null !== $value)
        {
            if (! $this->getLastCat())
            {
                $this->addcharstat('Other Info');
            }

            $this->stats[$this->getLastCat()][$label] = $value;
        }
        else
        {
            if (! isset($this->stats[$label]))
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
     * @param string $label The label associated with the value
     * @param string $val   The value of the attribute
     */
    public function setcharstat($cat, $label, $val)
    {
        if (! isset($this->stats[$cat][$label]))
        {
            $oldlabel = $this->getLastCat();
            // \Zend\Debug\Debug::dump($oldlabel);
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
        else if (isset($this->stats[$section]))
        {
            return $this->stats[$section];
        }
        else
        {
            return;
        }
    }

    /**
     * Resets the character stats array.
     */
    function wipeStats()
    {
        $this->lastCat = 'Other Info';
        $this->stats = [];

        return true;
    }

    /**
     * Get last category used.
     *
     * @return string
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
     *
     * @return array
     */
    public function getStats(): array
    {
        return $this->stats;
    }
}
