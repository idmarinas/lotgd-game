<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\Form\DataTransformer;

use DateTime;
use Symfony\Component\Form\DataTransformerInterface;

class DateTimeTypeTransformer implements DataTransformerInterface
{
    public function transform($data)
    {
        $default = new DateTime('0000-00-00 00:00:00');

        if ($default == $data)
        {
            $data = new DateTime('0000-01-01 00:00:00');
        }

        return $data;
    }

    public function reverseTransform($data)
    {
        $default = new DateTime('0000-01-01 00:00:00');

        if ($default == $data)
        {
            $data = new DateTime('0000-00-00 00:00:00');
        }

        return $data;
    }
}
