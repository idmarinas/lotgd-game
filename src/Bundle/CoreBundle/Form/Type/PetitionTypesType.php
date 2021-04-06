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

namespace Lotgd\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetitionTypesType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'petitions' => [],
        ]);
        parent::configureOptions($resolver);

        //-- Get petitions available.
        $choices = function (Options $options)
        {
            $choices = [];

            foreach ($options['petitions'] as $petition)
            {
                $choices[$petition] = $petition;
            }

            return $choices;
        };

        $resolver->setDefaults([
            'choices'            => $choices,
            'translation_domain' => 'lotgd_core_form_petition',
        ]);
    }
}
