<?php

namespace Lotgd\Ajax\Core;

use Jaxon\Response\Response;

class Mail
{
    /**
     * Check status of inbox.
     *
     * @return Response
     */
    public function status(): Response
    {
        global $session;

        $response = new Response();

        //-- Do nothing if there is no active session
        if (! ($session['user']['loggedin'] ?? false))
        {
            return $response;
        }

        $mail = \Doctrine::getRepository(\Lotgd\Core\Entity\Mail::class);
        $result = $mail->getCountMailOfCharacter((int) ($session['user']['acctid'] ?? 0));

        $response->html('ye-olde-mail-count-text', \LotgdTranslator::t('parts.mail.title', [
            'new' => $result['notSeenCount'],
            'old' => $result['seenCount']
        ], 'app-default'));

        return $response;
    }

    /**
     * Delete mail by ID.
     *
     * @param int    $mailId
     * @param string $textDomain
     *
     * @return Response
     */
    public function deleteMail(int $mailId, string $textDomain): Response
    {
        global $session;

        $response = new Response();

        //-- Do nothing if there is no active session
        if (! $session['user']['loggedin'])
        {
            return $response;
        }

        $repository = \Doctrine::getRepository('LotgdCore:Mail');
        $delete = $repository->findOneBy([
            'messageid' => $mailId,
            'msgto' => $session['user']['acctid']
        ]);

        $type = 'error';
        $message = \LotgdTranslator::t('dialog.del.one.error', [], $textDomain);

        if ($delete)
        {
            \Doctrine::remove($delete);
            \Doctrine::flush();

            invalidatedatacache("mail-{$session['user']['acctid']}");

            $type = 'success';
            $message = \LotgdTranslator::t('dialog.del.one.success', [], $textDomain);
        }

        $response->dialog->{$type}($message);
        $response->jQuery('#mail-read-buttons')->addClass('red')->children('.ui.button')->addClass('disabled')->removeClass('loading');
        $response->jQuery('#mail-row-'.$mailId)->remove();

        return $response;
    }

    /**
     * Delete mail in bulk by ID.
     *
     * @param string  $string
     * @param string $textDomain
     *
     * @return Response
     */
    public function deleteBulkMail(string $string, string $textDomain): Response
    {
        global $session;

        $response = new Response();

        //-- Do nothing if there is no active session
        if (! $session['user']['loggedin'])
        {
            return $response;
        }

        $post = [];
        parse_str($string, $post);
        $post = $post['msg'];

        $repository = \Doctrine::getRepository('LotgdCore:Mail');

        $count = $repository->deleteBulkMail($post);

        $type = 'error';
        $message = \LotgdTranslator::t('dialog.del.bulk.error', [], $textDomain);

        if ($count)
        {
            $type = 'success';
            $message = \LotgdTranslator::t('dialog.del.bulk.success', [], $textDomain);

            foreach ($post as $mailId)
            {
                $response->jQuery('#mail-row-'.$mailId)->remove();
            }
        }

        if (! count($post))
        {
            $message = \LotgdTranslator::t('dialog.del.bulk.empty', [], $textDomain);
        }

        $response->jQuery('.ui.delete.button, .toggle.lotgd.checkbox, #check_name_select, .ui.check.toggle.button')->removeClass('disabled');
        $response->jQuery('.ui.delbulk.button')->removeClass('loading disabled');
        $response->dialog->{$type}($message);

        return $response;
    }
}
