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

namespace Lotgd\Core\EntityForm\Companions;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbilitiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choice = [];

        for ($i = 0; $i <= 30; ++$i)
        {
            $choice[(string) $i] = (string) $i;
        }

        $builder->add('fighter', CheckboxType::class, [
            'label' => 'figther',
            // 'required' => false
        ])
            ->add('defend', CheckboxType::class, [
                'label' => 'defend',
            ])
            ->add('heal', ChoiceType::class, [
                'choices'    => $choice,
                'required'   => false,
                'empty_data' => '0',
            ])
            ->add('magic', ChoiceType::class, [
                'choices'    => $choice,
                'required'   => false,
                'empty_data' => '0',
            ])
        ;

        $builder->get('fighter')->addModelTransformer(new CallbackTransformer(
            fn($value) => (bool) $value,
            fn($value) => (int) $value
        ));
        $builder->get('defend')->addModelTransformer(new CallbackTransformer(
            fn($value) => (bool) $value,
            fn($value) => (int) $value
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'form_core_companions',
        ]);
    }
}
