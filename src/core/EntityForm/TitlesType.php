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

use Lotgd\Core\Entity\Titles;
use Lotgd\Core\Entity\TitlesTranslation;
use Lotgd\Core\EntityForm\Titles as CreaturesFieldType;
use Lotgd\Core\Form\Type\TranslatableFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TitlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dk', NumberType::class, [
                'label' => 'dk',
                'attr'  => [
                    'min'  => 0,
                    'step' => 1,
                ],
            ])
            ->add('male', TranslatableFieldType::class, [
                'personal_translation' => TitlesTranslation::class,
                'widget'               => CreaturesFieldType\TextTranslationType::class,
                'field'                => 'male',
                'label'                => 'male',
            ])
            ->add('female', TranslatableFieldType::class, [
                'personal_translation' => TitlesTranslation::class,
                'widget'               => CreaturesFieldType\TextTranslationType::class,
                'field'                => 'female',
                'label'                => 'female',
            ])
            ->add('save', SubmitType::class, ['label' => 'save.button'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Titles::class,
            'translation_domain' => 'form_core_titles',
        ]);
    }
}
