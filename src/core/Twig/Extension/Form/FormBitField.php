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

namespace Lotgd\Core\Twig\Extension\Form;

use Lotgd\Core\Form\Element\BitField;
use Lotgd\Core\Template\Theme as Environment;
use Twig\TwigFunction;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

class FormBitField extends FormSelect
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_bitfield', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render a form <select> element from the provided $element.
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     *
     * @return string
     */
    public function render(Environment $env, ElementInterface $element)
    {
        if (! $element instanceof BitField)
        {
            throw new Exception\InvalidArgumentException(sprintf('%s requires that the element is of type Lotgd\Core\Form\Element\BitField', __METHOD__));
        }

        $element->setAttribute('class', 'search selection '.$element->getAttribute('class'));
        $element->setAttribute('multiple', true); //-- BitField is always multiple

        $element->setValue($this->bitFieldValues($element->getValue(), $element->getDisabledMask()));

        //-- Now render as normal select
        return parent::render($env, $element);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-bit-field';
    }

    /**
     * Format bitfield values as array.
     */
    protected function bitFieldValues($value, array $disableMask): array
    {
        if (is_array($value))
        {
            return $value;
        }

        $optionValues = [];
        $current = 1;
        $bitfield = (int) $value;

        while ($current & 0x7FFFFFFF)
        {
            if ($current & $bitfield && ! ($disableMask[$current] ?? false))
            {
                $optionValues[] = $current;
            }

            $current <<= 1;
        }

        return $optionValues;
    }
}
