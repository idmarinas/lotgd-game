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

use Lotgd\Core\Event\Character;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RaceType extends ChoiceType
{
    private $eventDispatcher;
    private $translator;

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $args = new Character();
        $this->eventDispatcher->dispatch($args, Character::RACE_NAMES);
        $races = $args->getData();
        $races = array_flip($races);
        $races = [
            $this->translator->trans('character.racename', [], RACE_UNKNOWN) => RACE_UNKNOWN,
        ] + $races;

        $resolver->setDefaults([
            'attr' => [
                'class' => 'search selection lotgd',
            ],
            'choices' => $races,
        ]);

        return $resolver;
    }

    /** @required */
    public function setDispatcher(EventDispatcherInterface $dispatcher): self
    {
        $this->eventDispatcher = $dispatcher;

        return $this;
    }

    /** @required */
    public function setTranslator(TranslatorInterface $translatorInterface): self
    {
        $this->translator = $translatorInterface;

        return $this;
    }
}
