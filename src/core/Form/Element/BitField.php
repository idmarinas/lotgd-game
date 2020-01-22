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

use Lotgd\Core\Filter as LotgdFilter;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator;

class BitField extends Select implements InputProviderInterface
{
    protected $disabledMask = [];

    /**
     * Seed attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'select',
        'multiple' => true
    ];

    /**
     * Set options for an element. Accepted options are:
     * - disabled_mask: a list of disabled mask for element.
     *
     * {@inheritdoc}
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
     */
    public function getDisabledMask(): array
    {
        return $this->disabledMask;
    }

    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'filters' => [
                //-- Transform array into BitField (int)
                ['name' => LotgdFilter\BitField::class],
            ],
            'validators' => [
                ['name' => Validator\Digits::class]
            ],
        ];
    }
}
