<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Navigation\Pattern;

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

trait Menu
{

    /**
     * Add navs for actions of superuser.
     */
    public function superuser(): void
    {
        global $session;

        $superuser = $session['user']['superuser'];

        $this->addHeader('common.superuser.category', ['textDomain' => parent::DEFAULT_NAVIGATION_TEXT_DOMAIN]);

        if ($superuser & SU_EDIT_COMMENTS)
        {
            $this->addNav('common.superuser.moderate', 'moderate.php', ['textDomain' => parent::DEFAULT_NAVIGATION_TEXT_DOMAIN]);
        }

        if ($superuser & ~SU_DOESNT_GIVE_GROTTO)
        {
            $this->addNav('common.superuser.superuser', 'superuser.php', ['textDomain' => parent::DEFAULT_NAVIGATION_TEXT_DOMAIN]);
        }

        if ($superuser & SU_INFINITE_DAYS)
        {
            $this->addNav('common.superuser.newday', 'newday.php', ['textDomain' => parent::DEFAULT_NAVIGATION_TEXT_DOMAIN]);
        }
    }

    /**
     * Add navs for action of superuser in Grotto page.
     */
    public function superuserGrottoNav(): void
    {
        global $session;

        $superuser = $session['user']['superuser'];

        if ($superuser & ~SU_DOESNT_GIVE_GROTTO)
        {
            $script = $this->request->getServer('SCRIPT_NAME');

            if ('superuser.php' != $script)
            {
                $this->addNav('common.superuser.rsuperuser', 'superuser.php', ['textDomain' => parent::DEFAULT_NAVIGATION_TEXT_DOMAIN]);
            }
        }

        $this->addNav('common.superuser.mundane', 'village.php', ['textDomain' => parent::DEFAULT_NAVIGATION_TEXT_DOMAIN]);
    }

    /**
     * Add nav to village/shades.
     *
     * @param string $extra
     */
    public function villageNav($extra = ''): void
    {
        global $session;

        $extra = (false === \strpos($extra, '?') ? '?' : '');
        $extra = ('?' == $extra ? '' : $extra);

        $args = new GenericEvent();
        $this->dispatcher->dispatch($args, Events::PAGE_NAVIGATION_VILLAGE);
        $args = modulehook('villagenav', $args->getArguments());

        if ($args['handled'] ?? false)
        {
            return;
        }
        elseif ($session['user']['alive'])
        {
            $this->addNav('common.villagenav.village', "village.php{$extra}", [
                'textDomain' => parent::DEFAULT_NAVIGATION_TEXT_DOMAIN,
                'params'     => ['location' => $session['user']['location']],
            ]);

            return;
        }

        //-- User is dead
        $this->addNav('common.villagenav.shades', 'shades.php', ['textDomain' => parent::DEFAULT_NAVIGATION_TEXT_DOMAIN]);
    }
}
