<?php
/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Ajax\Pattern\Core\Mail;

use Jaxon\Response\Response;

trait Delete
{
    /**
     * Delete mail by ID.
     */
    public function deleteMail(int $mailId): Response
    {
        global $session;

        $check = $this->checkLoggedInRedirect();

        if (true !== $check)
        {
            return $check;
        }

        $response   = new Response();
        $repository = $this->getRepository();

        $delete = $repository->findOneBy([
            'messageid' => $mailId,
            'msgto'     => $session['user']['acctid'],
        ]);

        $type    = 'error';
        $message = \LotgdTranslator::t('dialog.del.one.error', [], $this->getTextDomain());

        if ($delete)
        {
            \Doctrine::remove($delete);
            \Doctrine::flush();

            $type    = 'success';
            $message = \LotgdTranslator::t('dialog.del.one.success', [], $this->getTextDomain());
        }

        $response->dialog->{$type}($message);
        $response->jQuery('#mail-read-buttons')->addClass('red')->children('.ui.button')->addClass('disabled')->removeClass('loading');
        $response->jQuery('#mail-row-'.$mailId)->remove();

        return $response;
    }

    /**
     * Delete mail in bulk by ID.
     */
    public function deleteBulkMail(array $post): Response
    {
        $check = $this->checkLoggedInRedirect();

        if (true !== $check)
        {
            return $check;
        }

        $response   = new Response();
        $repository = $this->getRepository();

        $post = $post['msg'];

        $count = $repository->deleteBulkMail($post);

        $type    = 'error';
        $message = \LotgdTranslator::t('dialog.del.bulk.error', [], $this->getTextDomain());

        if ($count)
        {
            $type    = 'success';
            $message = \LotgdTranslator::t('dialog.del.bulk.success', [], $this->getTextDomain());

            foreach ($post as $id)
            {
                $response->jQuery("#mail-row-{$id}")->remove();
            }
        }

        if ( \count($post) === 0)
        {
            $message = \LotgdTranslator::t('dialog.del.bulk.empty', [], $this->getTextDomain());
        }

        $response->jQuery('.ui.delete.button, .toggle.lotgd.checkbox, #check_name_select, .ui.check.toggle.button')->removeClass('disabled');
        $response->jQuery('.ui.delbulk.button')->removeClass('loading disabled');
        $response->dialog->{$type}($message);

        return $response;
    }
}
