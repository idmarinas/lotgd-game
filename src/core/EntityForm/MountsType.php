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

use Lotgd\Core\EntityForm\Common\BuffType;
use Lotgd\Core\Entity\Mounts;
use Lotgd\Core\Entity\MountsTranslation;
use Lotgd\Core\EntityForm\Mounts as FieldType;
use Lotgd\Core\Form\Type\LocationType;
use Lotgd\Core\Form\Type\TranslatableFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MountsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mountactive', CheckboxType::class, [
                'label'    => 'mountactive',
                'required' => false,
            ])
            ->add('mountname', TranslatableFieldType::class, [
                'personal_translation' => MountsTranslation::class,
                'widget'               => FieldType\NameTranslationType::class,
                'field'                => 'mountname',
                'label'                => 'mountname',
            ])
            ->add('mountdesc', TranslatableFieldType::class, [
                'personal_translation' => MountsTranslation::class,
                'widget'               => FieldType\TextareaTranslationType::class,
                'field'                => 'mountdesc',
                'label'                => 'mountdesc',
            ])
            ->add('mountcategory', TranslatableFieldType::class, [
                'personal_translation' => MountsTranslation::class,
                'widget'               => FieldType\CategoryTranslationType::class,
                'field'                => 'mountcategory',
                'label'                => 'mountcategory',
            ])
            ->add('mountlocation', LocationType::class, [
                'label' => 'mountlocation',
            ])
            ->add('mountdkcost', NumberType::class, [
                'label' => 'mountdkcost',
            ])
            ->add('mountcostgems', NumberType::class, [
                'label' => 'mountcostgems',
            ])
            ->add('mountfeedcost', NumberType::class, [
                'label' => 'mountfeedcost',
            ])
            ->add('mountforestfights', NumberType::class, [
                'label' => 'mountforestfights',
            ])
            ->add('newday', TranslatableFieldType::class, [
                'personal_translation' => MountsTranslation::class,
                'widget'               => FieldType\TextareaTranslationType::class,
                'field'                => 'newday',
                'label'                => 'newday',
            ])
            ->add('recharge', TranslatableFieldType::class, [
                'personal_translation' => MountsTranslation::class,
                'widget'               => FieldType\TextareaTranslationType::class,
                'field'                => 'recharge',
                'label'                => 'recharge',
            ])
            ->add('partrecharge', TranslatableFieldType::class, [
                'personal_translation' => MountsTranslation::class,
                'widget'               => FieldType\TextareaTranslationType::class,
                'field'                => 'partrecharge',
                'label'                => 'partrecharge',
            ])
            ->add('mountbuff', BuffType::class, [
                'label' => 'mountbuff',
            ])

            ->add('save', SubmitType::class, ['label' => 'save.button'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Mounts::class,
            'translation_domain' => 'form_core_mounts',
        ]);
    }
}
