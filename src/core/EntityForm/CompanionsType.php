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

use Lotgd\Core\Entity\Companions;
use Lotgd\Core\Entity\CompanionsTranslation;
use Lotgd\Core\EntityForm\Companions as CreaturesFieldType;
use Lotgd\Core\Form\Type\LocationType;
use Lotgd\Core\Form\Type\TranslatableFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('companionactive', CheckboxType::class, [
                'label'    => 'companion_active',
                'required' => false,
            ])
            ->add('name', TranslatableFieldType::class, [
                'personal_translation' => CompanionsTranslation::class,
                'widget'               => CreaturesFieldType\NameTranslationType::class,
                'field'                => 'name',
                'label'                => 'name',
            ])
            ->add('category', TranslatableFieldType::class, [
                'personal_translation' => CompanionsTranslation::class,
                'widget'               => CreaturesFieldType\CategoryTranslationType::class,
                'field'                => 'category',
                'label'                => 'category',
            ])
            ->add('description', TranslatableFieldType::class, [
                'personal_translation' => CompanionsTranslation::class,
                'widget'               => CreaturesFieldType\TextareaTranslationType::class,
                'field'                => 'description',
                'label'                => 'description',
            ])
            ->add('dyingtext', TranslatableFieldType::class, [
                'personal_translation' => CompanionsTranslation::class,
                'widget'               => CreaturesFieldType\TextTranslationType::class,
                'field'                => 'dyingtext',
                'label'                => 'dying_text',
            ])
            ->add('jointext', TranslatableFieldType::class, [
                'personal_translation' => CompanionsTranslation::class,
                'widget'               => CreaturesFieldType\TextareaTranslationType::class,
                'field'                => 'jointext',
                'label'                => 'join_text',
            ])
            ->add('companionlocation', LocationType::class, [
                'label' => 'companion_location',
            ])
            ->add('attack', NumberType::class, [
                'label' => 'attack',
            ])
            ->add('attackperlevel', NumberType::class, [
                'label' => 'attack_per_level',
            ])
            ->add('defense', NumberType::class, [
                'label' => 'defense',
            ])
            ->add('defenseperlevel', NumberType::class, [
                'label' => 'defense_per_level',
            ])
            ->add('maxhitpoints', NumberType::class, [
                'label' => 'max_hitpoints',
            ])
            ->add('maxhitpointsperlevel', NumberType::class, [
                'label' => 'max_hitpoints_per_level',
            ])
            ->add('cannotdie', CheckboxType::class, [
                'label'    => 'cannot_die',
                'required' => false,
            ])
            ->add('cannotbehealed', CheckboxType::class, [
                'label'    => 'cannot_be_healed',
                'required' => false,
            ])
            ->add('companioncostdks', NumberType::class, [
                'label' => 'cost_dk',
            ])
            ->add('companioncostgems', NumberType::class, [
                'label' => 'cost_gems',
            ])
            ->add('companioncostgold', NumberType::class, [
                'label' => 'cost_gold',
            ])

            ->add('allowinshades', CheckboxType::class, [
                'label'    => 'allow_in_shades',
                'required' => false,
            ])
            ->add('allowinpvp', CheckboxType::class, [
                'label'    => 'allow_in_pvp',
                'required' => false,
            ])
            ->add('allowintrain', CheckboxType::class, [
                'label'    => 'allow_in_train',
                'required' => false,
            ])
            ->add('abilities', CreaturesFieldType\AbilitiesType::class, [
                'label' => 'abilities',
            ])

            ->add('save', SubmitType::class, ['label' => 'save.button'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Companions::class,
            'translation_domain' => 'form_core_companions',
        ]);
    }
}
