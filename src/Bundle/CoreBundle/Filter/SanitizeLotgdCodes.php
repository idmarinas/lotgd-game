<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Filter;

use Laminas\Filter\AbstractFilter;

class SanitizeLotgdCodes extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter($string)
    {
        return \preg_replace('/[`´]./u', '', $string);
    }
}
