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
use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;

class Tagify extends Element implements InputProviderInterface
{
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
                ['name' => LotgdFilter\UnTagify::class],
            ],
            'validators' => [],
        ];
    }
}
