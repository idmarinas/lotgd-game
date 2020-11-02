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

trait Read
{
    /**
     * Read message.
     */
    public function read(?int $id): Response
    {
        global $session;

        $check = $this->checkLoggedInRedirect();

        if (true !== $check)
        {
            return $check;
        }

        try
        {
            $response   = new Response();
            $params     = $this->getParams();
            $repository = $this->getRepository();

            $message = $repository->findOneBy([
                'messageid' => $id,
                'msgto'     => $session['user']['acctid'],
            ]);

            if ( ! $message)
            {
                $response->dialog->error(\LotgdTranslator::t('jaxon.fail.read.not.found', [], $this->getTextDomain()));

                return $response;
            }

            $charRepository = \Doctrine::getRepository('LotgdCore:Characters');

            $params['message'] = clone $message;
            $params['sender']  = $charRepository->findOneByAcct($message->getMsgfrom());

            $params['paginator'] = $repository->getNextPreviousMail($message->getMessageid(), $session['user']['acctid']);

            //-- Mark as read
            $message->setSeen(true);

            \Doctrine::persist($message);
            \Doctrine::flush();

            // Dialog content
            $content = $this->getTemplate()->renderBlock('mail_read', $params);

            // Dialog title
            $title = \LotgdTranslator::t('title', [], $this->getTextDomain());

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
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

            $response->dialog->error(\LotgdTranslator::t('jaxon.fail.read', [], $this->getTextDomain()));
        }

        return $response;
    }

    /**
     * Mark message as unread.
     */
    public function unread(?int $id): Response
    {
        global $session;

        $check = $this->checkLoggedInRedirect();

        if (true !== $check)
        {
            return $check;
        }

        try
        {
            $response   = new Response();
            $repository = $this->getRepository();

            $unread = $repository->findOneBy([
                'messageid' => $id,
                'msgto'     => $session['user']['acctid'],
            ]);

            if ( ! $unread)
            {
                $response->dialog->error(\LotgdTranslator::t('jaxon.fail.unread.not.found', [], $this->getTextDomain()));

                return $response;
            }

            //-- Mark as unread
            $unread->setSeen(false);

            \Doctrine::persist($unread);
            \Doctrine::flush();

            \LotgdCache::removeItem("mail-{$session['user']['acctid']}");

            //-- Return to inbox
            $response = $this->inbox();
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->dialog->error(\LotgdTranslator::t('jaxon.fail.unread', [], $this->getTextDomain()));
        }

        return $response;
    }
}
