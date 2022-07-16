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

namespace Lotgd\Core\Twig\Extension\Pattern;

use Symfony\Component\EventDispatcher\GenericEvent;
use Twig\Environment as Environment;

trait CoreFunction
{
    /**
     * Add server name to url query.
     */
    public function baseUrl(string $query): string
    {
        return \sprintf('//%s/%s', $this->request->getServer('SERVER_NAME'), $query);
    }

    /**
     * Activate a hook.
     *
     * @param string $name
     * @param array  $data
     *
     * @return mixed
     */
    public function triggerEvent($name, $data = [])
    {
        $data = new GenericEvent(null, $data);
        $this->dispatcher->dispatch($name, $data);

        return $data->getArguments();
    }

    /**
     * Validating a protocol.
     *
     * @param string $protocol
     * @param mixed  $url
     *
     * @return bool
     */
    public function isValidProtocol($url)
    {
        // We should check all legeal protocols
        $protocols = ['http', 'https', 'ftp', 'ftps'];
        $protocol  = \explode(':', $url, 2);
        $protocol  = $protocol[0];

        // This will take care of download strings such as: not publically released or contact admin
        return \in_array($protocol, $protocols);
    }

    /**
     * Render a PvP table list.
     */
    public function pvpListTable(Environment $env, array $params): string
    {
        $params['linkBase']  = ($params['linkBase'] ?? 'pvp.php') ?: 'pvp.php';
        $params['linkExtra'] = ($params['linkExtra'] ?? '?act=attack') ?: '?act=attack';

        $params['linkAttack'] = "{$params['linkBase']}{$params['linkExtra']}";
        $params['linkAttack'] .= ($params['isInn'] ?? false) ? '&inn=1' : '';

        return $env->load('_blocks/_pvp.html.twig')->renderBlock('pvp_list', $params);
    }

    /**
     * Render a count of sleepers for zone.
     */
    public function pvpListSleepers(Environment $env, array $params): string
    {
        return $env->load('_blocks/_pvp.html.twig')->renderBlock('pvp_sleepers', $params);
    }

    /**
     * Get cookie name.
     */
    public function sessionCookieName(): string
    {
        return $this->session->getName();
    }

    /**
     * Dump var and return a string.
     *
     * @param mixed $var
     */
    public function varDump($var): string
    {
        return '<pre>'.\var_export($var, true).'</pre>';
    }
}
