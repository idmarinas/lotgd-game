<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Form\Element;

use Zend\Form\Element\Select;

class BitField extends Select
{
    protected $disabledMask = [];

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'select',
        'multiple' => true
    ];

    /**
     * Set options for an element. Accepted options are:
     * - disabled_mask: a list of disabled mask for element
     *
     * @inheritDoc
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($this->options['disabled_mask']))
        {
            $this->setDisabledMask($this->options['disabled_mask']);
        }

        return $this;
    }

    /**
     * Set disabled mask for bitfield.
     *
     * @param array $mask
     * @return $this
     */
    public function setDisabledMask(array $mask)
    {
        foreach ($mask as $key => $value)
        {
            if (is_bool($value))
            {
                $this->disabledMask[$key] = $value;

                continue;
            }

            $this->disabledMask[$value] = true;
        }

        return $this;
    }

    /**
     * Get disabled mask for bitfield.
     *
     * @return array
     */
    public function getDisabledMask(): array
    {
        return $this->disabledMask;
    }
}
