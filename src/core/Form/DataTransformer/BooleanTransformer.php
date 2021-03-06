<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class BooleanTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return (bool) $value;
    }

    public function reverseTransform($value)
    {
        return (int) $value;
    }
}
