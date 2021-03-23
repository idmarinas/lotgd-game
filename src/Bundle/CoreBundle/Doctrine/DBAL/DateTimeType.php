<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.6.0
 */

namespace Lotgd\Bundle\CoreBundle\Doctrine\DBAL;

use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType as TypesDateTimeType;

class DateTimeType extends TypesDateTimeType
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value)
        {
            return $value;
        }

        if ($value instanceof DateTimeInterface && '-0001-11-30' == $value->format('Y-m-d'))
        {
            return '0000-00-00 00:00:00';
        }

        return parent::convertToDatabaseValue($value, $platform);
    }
}
