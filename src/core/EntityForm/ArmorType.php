<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\EntityForm;

use Lotgd\Core\Entity\Armor;
use Lotgd\Core\Entity\ArmorTranslation;
use Lotgd\Core\EntityForm\Armor as FieldType;
use Lotgd\Core\Form\Type\TranslatableFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArmorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('armorname', TranslatableFieldType::class, [
                'personal_translation' => ArmorTranslation::class,
                'widget'               => FieldType\NameTranslationType::class,
                'field'                => 'armorname',
                'label'                => 'armorname',
            ])
            ->add('level', NumberType::class, [
                'label' => 'level',
            ])
            ->add('defense', RangeType::class, [
                'label' => 'defense',
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
            'data_class'         => Armor::class,
            'translation_domain' => 'form-core-grotto-armor',
        ]);
    }
}
