<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\Form\Type;

use LotgdEventDispatcher;
use LotgdTranslator;
use Lotgd\Core\Event\Character;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RaceType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $args = new Character();
        LotgdEventDispatcher::dispatch($args, Character::RACE_NAMES);
        $races = modulehook('racenames', $args->getData());
        $races = \array_flip($races);
        $races = [
            LotgdTranslator::t('character.racename', [], RACE_UNKNOWN) => RACE_UNKNOWN,
        ] + $races;

        $resolver->setDefaults([
            'attr' => [
                'class' => 'search selection lotgd',
            ],
            'choices' => $races,
        ]);

        return $resolver;
    }
}
