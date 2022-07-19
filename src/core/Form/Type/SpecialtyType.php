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

use Lotgd\Core\Event\Core;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SpecialtyType extends ChoiceType
{
    private $eventDispatcher;

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $specialties = new Core(['' => 'Undecided']);
        $this->eventDispatcher->dispatch($specialties, Core::SPECIALTY_NAMES);
        $specialties = $specialties->getData();
        $specialties = \array_flip($specialties);

        $resolver->setDefaults([
            'attr' => [
                'class' => 'search selection lotgd',
            ],
            'choices' => $specialties,
        ]);

        return $resolver;
    }

    /** @required */
    public function setDispatcher(EventDispatcherInterface $dispatcher): self
    {
        $this->eventDispatcher = $dispatcher;

        return $this;
    }
}
