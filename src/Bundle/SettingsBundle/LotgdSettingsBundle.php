<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\SettingsBundle;

use Acelaya\Doctrine\Type\PhpEnumType;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Lotgd\Bundle\SettingsBundle\Doctrine\FieldEnum\SettingType;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LotgdSettingsBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        if (\class_exists('Acelaya\Doctrine\Type\PhpEnumType') && ! DoctrineType::hasType('setting_type_enum'))
        {
            PhpEnumType::registerEnumType('setting_type_enum', SettingType::class);
        }
    }
}
