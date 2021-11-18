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

namespace Lotgd\Core\Bag;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

interface NotificationsBagInterface extends SessionBagInterface
{
    /**
     * Adds a notification for the given type.
     *
     * @param string $type
     * @param mixed  $notification
     */
    public function add($type, $notification);

    /**
     * Registers one or more notifications for a given type.
     *
     * @param string       $type
     * @param string|array $notifications
     */
    public function set($type, $notifications);

    /**
     * Gets notifications for a given type.
     *
     * @param string $type    Notification category type
     * @param array  $default Default value if $type does not exist
     *
     * @return array
     */
    public function peek($type, array $default = []);

    /**
     * Gets all notifications.
     *
     * @return array
     */
    public function peekAll();

    /**
     * Gets and clears from the stack.
     *
     * @param string $type
     * @param array  $default Default value if $type does not exist
     *
     * @return array
     */
    public function get($type, array $default = []);

    /**
     * Gets and clears from the stack.
     *
     * @return array
     */
    public function all();

    /**
     * Sets all notifications.
     */
    public function setAll(array $notifications);

    /**
     * Has notifications for a given type?
     *
     * @param string $type
     *
     * @return bool
     */
    public function has($type);

    /**
     * Returns a list of all defined types.
     *
     * @return array
     */
    public function keys();
}
