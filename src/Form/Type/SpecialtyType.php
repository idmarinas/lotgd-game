<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecialtyType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $specialties = ['' => 'Undecided'];
        \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_SPECIALTY_NAMES, null, $specialties);
        $specialties = modulehook('specialtynames', $specialties);
        $specialties = \array_flip($specialties);

        $resolver->setDefaults([
            'attr' => [
                'class' => 'search selection lotgd',
            ],
            'choices' => $specialties,
        ]);

        return $resolver;
    }
}
