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

namespace Lotgd\Core\Form\Type;

use Lotgd\Core\Event\Other;
use Lotgd\Core\Lib\Settings;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends ChoiceType
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $vname = $this->settings->getSetting('villagename', LOCATION_FIELDS);
        $locs  = new Other([
            $vname => [
                'location.village.of',
                ['name' => $vname],
                'app_default',
            ],
        ]);
        \LotgdEventDispatcher::dispatch($locs, Other::LOCATIONS);
        $locs        = modulehook('camplocs', $locs->getData());
        $locs['all'] = [
            'location.everywhere',
            [],
            'app_default',
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
