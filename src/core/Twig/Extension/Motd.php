<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\EntityRepository\MotdRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Motd extends AbstractExtension
{
    use Pattern\Motd;

    protected $doctrine;
    protected $repository;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('motd_show_item', [$this, 'display'], ['needs_environment' => true]),
            new TwigFunction('message_of_the_day', [$this, 'messageOfTheDay'], ['needs_environment' => true]),
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
            $this->repository = $this->doctrine->getRepository(\Lotgd\Core\Entity\Motd::class);
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
