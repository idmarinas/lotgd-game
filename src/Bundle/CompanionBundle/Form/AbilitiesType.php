<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CompanionBundle\Form;

use Lotgd\Bundle\CoreBundle\Form\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
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

        $builder
            ->add('fight', CheckboxType::class, ['required' => false, 'label' => 'entity.companion.abilities.figther'])
            ->add('defend', CheckboxType::class, ['required' => false, 'label' => 'entity.companion.abilities.defend'])
            ->add('heal', ChoiceType::class, [
                'label'      => 'entity.companion.abilities.heal',
                'choices'    => $choice,
                'required'   => false,
                'empty_data' => '0',
            ])
            ->add('magic', ChoiceType::class, [
                'label'      => 'entity.companion.abilities.magic',
                'choices'    => $choice,
                'required'   => false,
                'empty_data' => '0',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'lotgd_companion_admin',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'companion_abilities';
    }

    public function getName()
    {
        return 'companion_abilities_field';
    }
}
