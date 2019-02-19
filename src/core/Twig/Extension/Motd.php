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
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Motd extends AbstractExtension
{
    /**
     * Instance of MotdRepository.
     *
     * @var MotdRepository
     */
    protected $repository;

    /**
     * @param MotdRepository $repository
     */
    public function __construct(MotdRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('motd_show_item', [$this, 'display']),
        ];
    }

    /**
     * Display MoTD item or poll.
     *
     * @param array $motd
     * @param array $params Extra params
     *
     * @return string
     */
    public function display(array $motd, array $params = []): string
    {
        global $session;

        //-- Merge data
        $sub = $motd[0];
        unset($motd[0]);
        $motd = array_merge($sub, $motd);
        $params = array_merge(['motd' => $motd], $params);

        if ($motd['motdtype'])
        {
            $params['motd'] = $this->repository->appendPollResults($motd, $session['user']['acctid'] ?? null);

            return \LotgdTheme::renderThemeTemplate('pages/motd/parts/poll.twig', $params);
        }

        return \LotgdTheme::renderThemeTemplate('pages/motd/parts/item.twig', $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'motd';
    }
}
