<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form\Type;

use Lotgd\Core\Form\DataTransformer\BooleanTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType as TypeCheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class CheckboxType extends TypeCheckboxType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new BooleanTransformer());
    }
}
