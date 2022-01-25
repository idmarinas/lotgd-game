<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form\Type;

use LotgdKernel;
use Lotgd\Core\Lib\Settings;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetitionTypesType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        //-- Get petitions available in server.
        $settings  = LotgdKernel::get(Settings::class);
        $petitions = \explode(',', $settings->getSetting('petition_types'));

        $choices = [];

        foreach ($petitions as $petition)
        {
            $choices[$petition] = $petition;
        }

        $resolver->setDefaults([
            'choices'            => $choices,
            'translation_domain' => 'jaxon_petition',
        ]);
    }
}
