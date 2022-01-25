<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Bag\NotificationsBag;
use Lotgd\Core\Tool\Sanitize;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationsExtension extends AbstractExtension
{
    protected $session;
    protected $sanitize;

    public function __construct(SessionInterface $session, Sanitize $sanitize)
    {
        $this->session  = $session;
        $this->sanitize = $sanitize;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('show_notifications', [$this, 'display'], ['needs_environment' => true]),
        ];
    }

    /**
     * Show all notifications.
     */
    public function display(Environment $env): string
    {
        $bag = $this->session->getBag('notifications');
        $notifications = $bag->all();

        if (empty($notifications))
        {
            return '';
        }

        return $env->render('components/notifications.html.twig', [
            'notifications' => $notifications
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'notifications';
    }
}
