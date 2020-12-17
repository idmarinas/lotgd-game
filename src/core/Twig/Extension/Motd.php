<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\EntityRepository\MotdRepository;
use Lotgd\Core\Pattern as PatternCore;
use Twig\TwigFunction;

class Motd extends AbstractExtension
{
    use PatternCore\Doctrine;
    use PatternCore\Translator;
    use PatternCore\Template;
    use Pattern\Motd;

    protected $repository;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('motd_show_item', [$this, 'display']),
            new TwigFunction('message_of_the_day', [$this, 'messageOfTheDay']),
        ];
    }

    /**
     * Get repository of MotdRepository.
     *
     * @return MotdRepository
     */
    public function getMotdRepository()
    {
        if ( ! $this->repository instanceof MotdRepository)
        {
            $this->repository = $this->getDoctrineRepository(\Lotgd\Core\Entity\Motd::class);
        }

        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'motd';
    }
}
