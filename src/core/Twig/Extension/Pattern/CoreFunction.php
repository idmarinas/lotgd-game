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

namespace Lotgd\Core\Twig\Extension\Pattern;

trait CoreFunction
{
    /**
     * Undocumented function
     *
     * @param array $title
     *
     * @return string
     */
    public function pageTitle(array $title)
    {
        return \LotgdTranslator::t($title['title'], $title['params'], $title['textDomain']);
    }

    /**
     * Get version of game.
     *
     * @return string
     */
    public function gameVersion(): string
    {
        return \Lotgd\Core\Application::VERSION;
    }

    /**
     * Get copyright of game.
     *
     * @return string
     */
    public function gameCopyright(): string
    {
        return \Lotgd\Core\Application::LICENSE . \Lotgd\Core\Application::COPYRIGHT;
    }

    /**
     * Get value of setting.
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function getsetting($name, $default): ?string
    {
        return getsetting($name, $default);
    }

    /**
     * Activate a hook.
     *
     * @param string $name
     * @param array  $data
     *
     * @return mixed
     */
    public function modulehook($name, $data)
    {
        return modulehook($name, $data);
    }

    /**
     * Validating a protocol.
     *
     * @param string $protocol
     *
     * @return bool
     */
    public function isValidProtocol($url)
    {
        // We should check all legeal protocols
        $protocols = ['http', 'https', 'ftp', 'ftps'];
        $protocol = explode(':', $url, 2);
        $protocol = $protocol[0];

        // This will take care of download strings such as: not publically released or contact admin
        return in_array($protocol, $protocols);
    }

    /**
     * Time in the game.
     *
     * @return string
     */
    public function gametime()
    {
        return getgametime();
    }

    /**
     * Seconds to next game day.
     *
     * @return int
     */
    public function secondstonextgameday()
    {
        return secondstonextgameday();
    }
}
