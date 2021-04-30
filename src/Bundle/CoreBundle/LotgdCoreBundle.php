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

namespace Lotgd\Bundle\CoreBundle;

use Acelaya\Doctrine\Type\PhpEnumType;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Lotgd\Bundle\CoreBundle\DependencyInjection\Compiler\GlobalVariablesCompilerPass;
use Lotgd\Bundle\CoreBundle\Doctrine\FieldEnum\PetitionStatusTypeEnum;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LotgdCoreBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        if (\class_exists('Acelaya\Doctrine\Type\PhpEnumType') && ! DoctrineType::hasType('petition_status_type_enum'))
        {
            PhpEnumType::registerEnumType('petition_status_type_enum', PetitionStatusTypeEnum::class);
        }
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new GlobalVariablesCompilerPass());
    }
}
