<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\SettingsBundle\Event;

use Lotgd\Bundle\SettingsBundle\Entity\Setting;
use Symfony\Contracts\EventDispatcher\Event;

class SettingsEvent extends Event
{
    public const PRE_UPDATE_SETTING  = 'lotgd_settings.update.pre';
    public const POST_UPDATE_SETTING = 'lotgd_settings.update.post';

    public const PRE_CREATE_SETTING  = 'lotgd_settings.create.pre';
    public const POST_CREATE_SETTING = 'lotgd_settings.create.post';

    public const PRE_DELETE_SETTING  = 'lotgd_settings.delete.pre';
    public const POST_DELETE_SETTING = 'lotgd_settings.delete.post';

    private $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function getSetting(): ?Setting
    {
        return $this->setting;
    }
}
