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

/**
 * Trait to navigation display.
 */
trait News
{
    public function showNewsItem($item)
    {
        //-- News that use new format
        if ($item['newFormat'])
        {
            //-- Special format for death messages
            if ('deathmessage' == $item['text'])
            {
                $deathmessage = $item['arguments']['deathmessage'] ?? [];
                $taunt = $item['arguments']['taunt'] ?? [];

                $message = '';

                if (count($deathmessage))
                {
                    $msg = $this->getTranslator()->trans($deathmessage['deathmessage'], $deathmessage['params'] ?? [], $deathmessage['textDomain']);

                    //-- Use default death message if translator not find translation.
                    if ($msg == $deathmessage['deathmessage'])
                    {
                        $msg = $this->getTranslator()->trans('default', $deathmessage['params'] ?? [], $deathmessage['textDomain']);
                    }

                    $message .= $msg;
                }

                if (count($taunt))
                {
                    $msg = $this->getTranslator()->trans($taunt['taunt'], $taunt['params'] ?? [], $taunt['textDomain']);

                    //-- Use default taunt if translator not find translation.
                    if ($msg == $taunt['taunt'])
                    {
                        $msg = $this->getTranslator()->trans('default', $taunt['params'] ?? [], $taunt['textDomain']);
                    }

                    $message .= ($message ? '`n' : '').$msg;
                }

                return $message;
            }

            return $this->getTranslator()->trans($item['text'], $item['arguments'] ?? [], $item['textDomain']);
        }

        //-- This old of make news, are deprecated and deleted in future version.
        //-- Delete old news of your server.
        $message = translate($item['text'], $item['textDomain']);

        return $this->sprintfnews(\str_replace('`%', '`%%', $message), $item['arguments'] ?? []);
    }
}
