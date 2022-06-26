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

use Lotgd\Core\Entity\Creatures;
use Lotgd\Core\Entity\CreaturesTranslation;
use Lotgd\Core\EntityForm\Creatures as CreaturesFieldType;
use Lotgd\Core\Form\Type\CreatureAiType;
use Lotgd\Core\Form\Type\TranslatableFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class CreaturesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creatureimage', TextType::class, [
                'label'    => 'creatureimage',
                'required' => false,
            ])
            ->add('creaturecategory', TranslatableFieldType::class, [
                'personal_translation' => CreaturesTranslation::class, //-- Mandatory
                'widget'               => CreaturesFieldType\CategoryTranslationType::class,
                'field'                => 'creaturecategory', //-- Mandatory
                'label'                => 'creaturecategory',
                'required'             => false,
            ])
            ->add('creaturename', TranslatableFieldType::class, [
                'personal_translation' => CreaturesTranslation::class,
                'widget'               => CreaturesFieldType\NameTranslationType::class,
                'field'                => 'creaturename',
                'label'                => 'creaturename',
            ])
            ->add('creatureweapon', TranslatableFieldType::class, [
                'personal_translation' => CreaturesTranslation::class,
                'widget'               => CreaturesFieldType\WeaponTranslationType::class,
                'field'                => 'creatureweapon',
                'label'                => 'creatureweapon',
            ])
            ->add('creaturedescription', TranslatableFieldType::class, [
                'personal_translation' => CreaturesTranslation::class,
                'widget'               => CreaturesFieldType\DescriptionTranslationType::class,
                'field'                => 'creaturedescription',
                'label'                => 'creaturedescription',
                'required'             => false,
            ])
            ->add('creaturewin', TranslatableFieldType::class, [
                'personal_translation' => CreaturesTranslation::class,
                'widget'               => CreaturesFieldType\WinTranslationType::class,
                'field'                => 'creaturewin',
                'label'                => 'creaturewin',
                'required'             => false,
            ])
            ->add('creaturelose', TranslatableFieldType::class, [
                'personal_translation' => CreaturesTranslation::class,
                'widget'               => CreaturesFieldType\LoseTranslationType::class,
                'field'                => 'creaturelose',
                'label'                => 'creaturelose',
                'required'             => false,
            ])
            ->add('creaturegoldbonus', NumberType::class, [
                'label' => 'creaturegoldbonus',
                'help'  => 'creaturegoldbonus_help',
                'html5' => true,
                'attr'  => [
                    'min'                   => 0,
                    'max'                   => 99.99,
                    'step'                  => 0.01,
                ],
            ])
            ->add('creaturedefensebonus', NumberType::class, [
                'label' => 'creaturedefensebonus',
                'help'  => 'creaturedefensebonus_help',
                'html5' => true,
                'attr'  => [
                    'min'                   => 0,
                    'max'                   => 99.99,
                    'step'                  => 0.01,
                ],
            ])
            ->add('creatureattackbonus', NumberType::class, [
                'label' => 'creatureattackbonus',
                'help'  => 'creatureattackbonus_help',
                'html5' => true,
                'attr'  => [
                    'min'                   => 0,
                    'max'                   => 99.99,
                    'step'                  => 0.01,
                ],
            ])
            ->add('creaturehealthbonus', NumberType::class, [
                'label' => 'creaturehealthbonus',
                'help'  => 'creaturehealthbonus_help',
                'html5' => true,
                'attr'  => [
                    'min'                   => 0,
                    'max'                   => 99.99,
                    'step'                  => 0.01,
                ],
            ])
            ->add('creatureaiscript', CreatureAiType::class, [
                'label'    => 'creatureaiscript',
                'required' => false,
            ])
            ->add('forest', CheckboxType::class, [
                'label'    => 'forest',
                'required' => false,
            ])
            ->add('graveyard', CheckboxType::class, [
                'label'    => 'graveyard',
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'save.button'])
        ;

        $callback = new CallbackTransformer(
            fn($toString) => (float) $toString,
            fn($toFloat) => (string) $toFloat
        );

        $builder->get('creaturegoldbonus')->addModelTransformer($callback);
        $builder->get('creaturedefensebonus')->addModelTransformer($callback);
        $builder->get('creatureattackbonus')->addModelTransformer($callback);
        $builder->get('creaturehealthbonus')->addModelTransformer($callback);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Creatures::class,
            'translation_domain' => 'form_core_creature',
        ]);
    }
}
