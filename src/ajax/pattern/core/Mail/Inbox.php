<?php
/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Ajax\Pattern\Core\Mail;

use Jaxon\Response\Response;
use Tracy\Debugger;

trait Inbox
{
    /**
     * Inbox of character.
     */
    public function inbox(?string $sortOrder = 'date', ?int $sortDirection = null): Response
    {
        global $session;

        $check = $this->checkLoggedInRedirect();

        if (true !== $check)
        {
            return $check;
        }

        try
        {
            $response      = new Response();
            $params        = $this->getParams();
            $repository    = $this->getRepository();
            $sortOrder     = (string) ($sortOrder ?: 'date');
            $sortDirection = (int) $sortDirection;

            $params['inboxLimit']    = getsetting('inboxlimit', 50);
            $params['oldMail']       = getsetting('oldmail', 14);
            $params['newDirection']  = (int) ! $sortDirection;
            $params['sortDirection'] = $sortDirection;
            $params['sortOrder']     = $sortOrder;

            $params['mails']   = $repository->getCharacterMail($session['user']['acctid'], $sortOrder, $sortDirection);
            $params['senders'] = $repository->getMailSenderNames($session['user']['acctid']);

            $params['inboxCount'] = \count($params['mails']);

            // Dialog content
            $content = $this->getTemplate()->renderBlock('mail_inbox', $params);

            // Dialog title
            $title = \LotgdTranslator::t('title', [], $this->getTextDomain());

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app_default'),
                    'class' => 'ui red deny button',
                ],
            ];

            //-- Options
            $options = [
                'autofocus' => false,
            ];

            $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons, $options);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->dialog->error(\LotgdTranslator::t('jaxon.fail.inbox', [], $this->getTextDomain()));
        }

        $response->jQuery('#mail-button')->removeClass('loading disabled');

        return $response;
    }
}
