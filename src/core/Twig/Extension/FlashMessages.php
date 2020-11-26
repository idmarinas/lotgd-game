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

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Component\FlashMessages as CoreFlashMessages;
use Lotgd\Core\Pattern as PatternCore;
use Twig\TwigFunction;

class FlashMessages extends AbstractExtension
{
    use PatternCore\Template;
    use PatternCore\Sanitize;

    protected $flashMessages;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('show_flash_messages', [$this, 'display']),
        ];
    }

    /**
     * Show all messages.
     *
     * @return string
     */
    public function display()
    {
        $container = $this->getFlashMessages()->getMessages();
        $output    = '';

        foreach ($container as $type => $messages)
        {
            foreach ($messages as $id => $message)
            {
                if (is_array($message))
                {
                    $message['message'] = $this->getSanitize()->fullSanitize($message['message']);
                    $message['id']      = $message['id']    ?? $id;
                    $message['class']   = $message['class'] ?? $type;
                    $message['close']   = $message['close'] ?? true;

                    $output .= $this->getTemplate()->render('{theme}/semantic/collection/message.html.twig', $message);

                    continue;
                }

                $output .= $this->getTemplate()->render('{theme}/semantic/collection/message.html.twig', [
                    'message' => $this->getSanitize()->fullSanitize($message),
                    'class'   => $type,
                    'close'   => true,
                    'id'      => $id,
                ]);
            }
        }

        $this->getFlashMessages()->clearMessagesFromContainer();

        return $output;
    }

    /**
     * Get instance of FlashMessages instance.
     */
    public function getFlashMessages(): CoreFlashMessages
    {
        if ( ! $this->flashMessages instanceof CoreFlashMessages)
        {
            $this->flashMessages = $this->getContainer(CoreFlashMessages::class);
        }

        return $this->flashMessages;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'flash-messages';
    }
}
