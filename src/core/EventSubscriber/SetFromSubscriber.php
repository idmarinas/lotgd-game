<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.1.0
 */

namespace Lotgd\Core\EventSubscriber;

use Lotgd\Core\Lib\Settings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Subscriber to add From to email if not have one.
 */
class SetFromSubscriber implements EventSubscriberInterface
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function onMessage(MessageEvent $event)
    {
        $email = $event->getMessage();

        if ( ! $email instanceof Email)
        {
            return;
        }

        if (empty($email->getFrom()))
        {
            $email->from(new Address(
                $this->settings->getSetting('gameadminemail', 'postmaster@localhost.com') ?: 'postmaster@localhost.com',
                $this->settings->getSetting('servername', 'The Legend of the Green Dragon')
            ));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }
}
