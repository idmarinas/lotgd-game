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

use Lotgd\Core\Tool\Sanitize;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashMessages extends AbstractExtension
{
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session  = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('show_flash_messages', [$this, 'display'], ['needs_environment' => true]),
        ];
    }

    /**
     * Show all messages.
     *
     * @return string
     */
    public function display(Environment $env)
    {
        $bag = $this->session->getFlashBag();
        $flashes = $bag->all();

        if (empty($flashes))
        {
            return '';
        }

        return $env->render('components/_flash_messages.html.twig', [
            'flashes' => $flashes
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'flash-messages';
    }
}
