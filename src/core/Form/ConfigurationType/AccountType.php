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

namespace Lotgd\Core\Form\ConfigurationType;

use Lotgd\Core\Form\Type\BitFieldType;
use Lotgd\Core\Form\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AccountType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('defaultsuperuser', BitFieldType::class, [
            'required' => false,
            'choices' => [
                'account.defaultsuperuser.options.infinite.days' => SU_INFINITE_DAYS,
                'account.defaultsuperuser.options.view.source'   => SU_VIEW_SOURCE,
                'account.defaultsuperuser.options.developer'     => SU_DEVELOPER,
                'account.defaultsuperuser.options.debug.output'  => SU_DEBUG_OUTPUT,
            ],
            'label' => 'account.defaultsuperuser.label',
        ])
            ->add('newplayerstartgold', NumberType::class, [
                'required' => false,
                'empty_data' => 50,
                'label'      => 'account.newplayerstartgold',
                'constraints' => [
                    new Assert\DivisibleBy(1)
                ]
            ])
            ->add('maxrestartgold', NumberType::class, [
                'required' => false,
                'empty_data' => 50,
                'label'      => 'account.maxrestartgold',
                'constraints' => [
                    new Assert\DivisibleBy(1)
                ]
            ])
            ->add('maxrestartgems', NumberType::class, [
                'required' => false,
                'empty_data' => 10,
                'label'      => 'account.maxrestartgems',
                'constraints' => [
                    new Assert\DivisibleBy(1)
                ]
            ])
            ->add('playerchangeemail', CheckboxType::class, [
                'required' => false,
                'label' => 'account.playerchangeemail',
            ])
            ->add('playerchangeemailauto', CheckboxType::class, [
                'required' => false,
                'label' => 'account.playerchangeemailauto.label',
                'help'  => 'account.playerchangeemailauto.note',
            ])
            ->add('playerchangeemaildays', RangeType::class, [
                'required' => false,
                'attr' => [
                    'min'  => 1,
                    'max'  => 30,
                    'step' => 1,
                ],
                'empty_data' => 1,
                'label'      => 'account.playerchangeemaildays',
                'constraints' => [
                    new Assert\DivisibleBy(1)
                ]
            ])
            ->add('validationtarget', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'account.validationtarget.options.old' => 0,
                    'account.validationtarget.options.new' => 1,
                ],
                'label' => 'account.validationtarget.label',
                'help'  => 'account.validationtarget.note',
            ])
            ->add('requireemail', CheckboxType::class, [
                'required' => false,
                'label' => 'account.requireemail',
            ])
            ->add('requirevalidemail', CheckboxType::class, [
                'required' => false,
                'label' => 'account.requirevalidemail',
            ])
            ->add('blockdupeemail', CheckboxType::class, [
                'required' => false,
                'label' => 'account.blockdupeemail',
            ])
            ->add('spaceinname', CheckboxType::class, [
                'required' => false,
                'label' => 'account.spaceinname',
            ])
            ->add('allowoddadminrenames', CheckboxType::class, [
                'required' => false,
                'label' => 'account.allowoddadminrenames',
            ])
            ->add('selfdelete', CheckboxType::class, [
                'required' => false,
                'label' => 'account.selfdelete',
            ])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'translation_domain' => 'form-core-grotto-configuration',
        ]);
    }
}
