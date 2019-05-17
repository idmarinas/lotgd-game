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
use Lotgd\Core\ServiceManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashMessages extends AbstractExtension
{
    use PatternCore\Container;
    use PatternCore\Sanitize;

    protected $flashMessages;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->setContainer($serviceManager);
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
        $container = $this->getFlashMessages()->getMessages();
        $output = '';

        foreach ($container as $type => $messages)
        {
            foreach ($messages as $id => $message)
            {
                if (is_array($message))
                {
                    $message['message'] = $this->getSanitize()->fullSanitize($message['message']);
                    $message['id'] = $message['id'] ?? $id;
                    $message['class'] = $message['class'] ?? $type;
                    $message['close'] = $message['close'] ?? true;

                    $output .= \LotgdTheme::renderLotgdTemplate('semantic/collection/message.twig', $message);

                    continue;
                }

                $output .= \LotgdTheme::renderLotgdTemplate('semantic/collection/message.twig', [
                    'message' => $this->getSanitize()->fullSanitize($message),
                    'class' => $type,
                    'close' => true,
                    'id' => $id
                ]);
            }
        }

        $this->getFlashMessages()->clearMessagesFromContainer();

        return $output;
    }

    /**
     * Get instance of FlashMessages instance.
     *
     * @return CoreFlashMessages
     */
    public function getFlashMessages(): CoreFlashMessages
    {
        if (! $this->flashMessages instanceof CoreFlashMessages)
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
