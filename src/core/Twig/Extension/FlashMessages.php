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

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Tool\Sanitize;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashMessages extends AbstractExtension
{
    protected $session;
    protected $sanitize;

    public function __construct(SessionInterface $session, Sanitize $sanitize)
    {
        $this->session  = $session;
        $this->sanitize = $sanitize;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('show_flash_messages', [$this, 'display'], ['needs_environment' => true]),
        ];
    }

    /**
     * Show all messages.
     *
     * @return string
     */
    public function display(Environment $env)
    {
        $output   = '';
        $flashBag = $this->session->getFlashBag();

        foreach ($flashBag->all() as $type => $messages)
        {
            foreach ($messages as $id => $message)
            {
                $messageId = "{$type}-{$id}";

                if (\is_array($message))
                {
                    $message['message'] = $this->sanitize->fullSanitize($message['message']);
                    $message['id']      = $message['id'] ?? $messageId;
                    $message['class']   = ($message['class'] ?? '').' '.$type;
                    $message['close']   = $message['close'] ?? true;

                    $output .= $env->render('semantic/collection/message.html.twig', $message);

                    continue;
                }

                $output .= $env->render('semantic/collection/message.html.twig', [
                    'message' => $this->sanitize->fullSanitize($message),
                    'class'   => $type,
                    'close'   => true,
                    'id'      => $messageId,
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
