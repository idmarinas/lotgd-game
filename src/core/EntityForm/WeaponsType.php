<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\EntityForm;

use Lotgd\Core\Entity\Weapons;
use Lotgd\Core\Entity\WeaponsTranslation;
use Lotgd\Core\EntityForm\Weapons as FieldType;
use Lotgd\Core\Form\Type\TranslatableFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeaponsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('weaponname', TranslatableFieldType::class, [
                'personal_translation' => WeaponsTranslation::class,
                'widget'               => FieldType\NameTranslationType::class,
                'field'                => 'weaponname',
                'label'                => 'weaponname',
            ])
            ->add('level', NumberType::class, [
                'label' => 'level',
                'html5' => true
            ])
            ->add('damage', RangeType::class, [
                'label' => 'damage',
                'attr'  => [
                    'min'                   => 1,
                    'max'                   => 15,
                    'disable_slider_labels' => false,
                    'step'                  => 1,
                ],
            ])

            ->add('save', SubmitType::class, ['label' => 'save.button'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Weapons::class,
            'translation_domain' => 'form_core_weapons',
        ]);
    }
}
