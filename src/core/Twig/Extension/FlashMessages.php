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
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashMessages extends AbstractExtension
{
    protected $flashMessages;

    /**
     * @param CoreFlashMessages $flashMessages
     */
    public function __construct(CoreFlashMessages $flashMessages)
    {
        $this->flashMessages = $flashMessages;
    }

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
        $container = $this->flashMessages->getMessages();
        $output = '';

        foreach ($container as $type => $messages)
        {
            foreach ($messages as $id => $message)
            {
                $output .= \LotgdTheme::renderLotgdTemplate('semantic/collection/message.twig', [
                    'message' => $message,
                    'class' => $type,
                    'close' => true,
                    'id' => $id
                ]);
            }
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'flash-messages';
    }
}
