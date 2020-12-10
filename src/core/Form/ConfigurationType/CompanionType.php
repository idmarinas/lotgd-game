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

use Lotgd\Core\Form\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CompanionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Enable the usage of companions
            ->add('enablecompanions', CheckboxType::class, [
                'required' => false,
                'label' => 'companion.enablecompanions',
            ])
            // How many companions are allowed per player
            ->add('companionsallowed', NumberType::class, [
                'required' => false,
                'label' => 'companion.companionsallowed.label',
                'note' => 'companion.companionsallowed.note',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero()
                ]
            ])
            // Are companions allowed to level up?
            ->add('companionslevelup', CheckboxType::class, [
                'required' => false,
                'label' => 'companion.companionslevelup',
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
