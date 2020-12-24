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
    use PatternCore\Sanitize;
    use PatternCore\Template;

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
        $output   = '';
        $flashBag = \LotgdKernel::get('session')->getFlashBag();

        foreach ($flashBag->all() as $type => $messages)
        {
            foreach ($messages as $id => $message)
            {
                $messageId = "{$type}-{$id}";

                if (\is_array($message))
                {
                    $message['message'] = $this->getSanitize()->fullSanitize($message['message']);
                    $message['id']      = $message['id'] ?? $messageId;
                    $message['class']   = ($message['class'] ?? '').' '.$type;
                    $message['close']   = $message['close'] ?? true;

                    $output .= $this->getTemplate()->render('{theme}/semantic/collection/message.html.twig', $message);

                    continue;
                }

                $output .= $this->getTemplate()->render('{theme}/semantic/collection/message.html.twig', [
                    'message' => $this->getSanitize()->fullSanitize($message),
                    'class'   => $type,
                    'close'   => true,
                    'id'      => $messageId,
                ]);
            }
        }

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
