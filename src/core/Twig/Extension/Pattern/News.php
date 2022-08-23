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

/**
 * Trait to navigation display.
 */
trait News
{
    public function showNewsItem($item)
    {
        $arguments = is_array($item['arguments']) ? $item['arguments'] : [];
        $arguments = is_string($item['arguments']) ? [$item['arguments']] : $arguments;

        //-- News that use new format
        if ($item['newFormat'])
        {
            //-- Special format for death messages
            if ('deathmessage' == $item['text'])
            {
                $deathmessage = $item['arguments']['deathmessage'] ?? [];
                $taunt        = $item['arguments']['taunt']        ?? [];

                $message = '';

                if ( ! empty($deathmessage))
                {
                    $msg = $this->translator->trans($deathmessage['deathmessage'], $deathmessage['params'] ?? [], $deathmessage['textDomain']);

                    //-- Use default death message if translator not find translation.
                    if ($msg == $deathmessage['deathmessage'])
                    {
                        $msg = $this->translator->trans('default', $deathmessage['params'] ?? [], $deathmessage['textDomain']);
                    }

                    $message .= $msg;
                }

                if ( ! empty($taunt))
                {
                    $msg = $this->translator->trans($taunt['taunt'], $taunt['params'] ?? [], $taunt['textDomain']);

                    //-- Use default taunt if translator not find translation.
                    if ($msg == $taunt['taunt'])
                    {
                        $msg = $this->translator->trans('default', $taunt['params'] ?? [], $taunt['textDomain']);
                    }

                    $message .= ($message !== '' && $message !== '0' ? '`n' : '').$msg;
                }

                return $message;
            }

            return $this->translator->trans($item['text'], $arguments, $item['textDomain']);
        }

        //-- This old of make news, are deprecated and deleted in future version.
        //-- Delete old news of your server.

        return $this->sprintfnews(str_replace('`%', '`%%', $item['text']), $arguments);
    }
}
