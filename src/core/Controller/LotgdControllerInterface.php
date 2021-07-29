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

namespace Lotgd\Core\Controller;

/**
 * Interface for migrating module to bundle.
 *
 * Implement in your Bundle controllers that need use runmodule.php
 *  -   Replace `runmodule.php?module=MODULE_NAME` for `runmodule.php?controller=CONTROLLER_NAME&method=METHOD_NAME`
 */
interface LotgdControllerInterface
{
    /**
     * Allow anonymous user acces to this controller?
     */
    public function allowAnonymous(): bool;

    /**
     * Override forced nav?.
     */
    public function overrideForcedNav(): bool;
}
