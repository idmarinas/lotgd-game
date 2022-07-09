<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Pvp;

use Lotgd\Core\Event\Character;
use Lotgd\Core\Lib\Settings;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Warning
{
    public const TRANSLATION_DOMAIN = 'page_pvp';

    private $dispatcher;
    private $settings;
    private $flash;
    private $translator;

    public function __construct(EventDispatcherInterface $dispatcher, Settings $settings, FlashBagInterface $flash, TranslatorInterface $translator)
    {
        $this->settings   = $settings;
        $this->dispatcher = $dispatcher;
        $this->flash      = $flash;
        $this->translator = $translator;
    }

    public function warning(bool $doKill = false)
    {
        global $session;

        $days = (int) $this->settings->getSetting('pvpimmunity', 5);
        $exp  = (int) $this->settings->getSetting('pvpminexp', 1500);

        if (
            $session['user']['age'] <= $days
            && 0 == $session['user']['dragonkills']
            && 0 == $session['user']['pk']
            && $session['user']['experience'] <= $exp
        ) {
            if ($doKill)
            {
                $this->flash->add('warning', $this->translator->trans('flash.message.warning.pk', [], self::TRANSLATION_DOMAIN));
                $session['user']['pk'] = 1;
            }
            else
            {
                $this->flash->add('warning', $this->translator->trans('flash.message.warning.msg', ['days' => $days, 'exp' => $exp], self::TRANSLATION_DOMAIN));
            }
        }

        $args = new Character(['dokill' => $doKill]);
        $this->dispatcher->dispatch($args, Character::PVP_DO_KILL);
    }
}
