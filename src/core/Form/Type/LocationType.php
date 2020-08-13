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

namespace Lotgd\Core\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $vname = getsetting('villagename', LOCATION_FIELDS);
        $locs  = [
            $vname => [
                'location.village.of',
                ['name' => $vname],
                'app-default',
            ],
        ];
        $locs        = modulehook('camplocs', $locs);
        $locs['all'] = [
            'location.everywhere',
            [],
            'app-default',
        ];
        ksort($locs);

        $defaultChoice = [];

        foreach ($locs as $loc => $params)
        {
            $value = \LotgdTranslator::t($params[0], $params[1], $params[2]);

            $defaultChoice[$value] = $loc;
        }

        $resolver->setDefaults([
            'attr' => [
                'class' => 'search selection lotgd',
            ],
            'choices' => $defaultChoice,
        ]);

        return $resolver;
    }
}
