<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class NumberTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return (int) $value;
    }

    public function reverseTransform($value)
    {
        return (int) $value;
    }
}
