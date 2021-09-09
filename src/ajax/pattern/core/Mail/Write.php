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
use Tracy\Debugger;

trait Write
{
    /**
     * Write message for a user.
     */
    public function write(?int $toPlayer = null)
    {
        $check = $this->checkLoggedInRedirect();

        if (true !== $check)
        {
            return $check;
        }

        try
        {
            $response   = new Response();
            $params     = $this->getParams();
            $repository = $this->getAcctRepository();

            if ($toPlayer)
            {
                $account = $repository->find($toPlayer);

                if ($account)
                {
                    $params['row'] = [
                        'acctid' => $account->getAcctid(),
                        'name'   => $account->getCharacter()->getName(),
                    ];
                }
            }

            $this->composer($params, $response);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->dialog->error(\LotgdTranslator::t('jaxon.fail.reply', [], $this->getTextDomain()));
        }

        return $response;
    }

    /**
     * Reply to a message.
     */
    public function reply(?int $reply): Response
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

            $row = $repository->replyToMessage($reply, $session['user']['acctid']);

            if ( ! $row)
            {
                $response->dialog->error(\LotgdTranslator::t('jaxon.fail.message.not.found', [], $this->getTextDomain()));

                return $response;
            }

            $subj = \LotgdTranslator::t('section.write.reply.subject', [
                'subject' => '',
            ], $this->getTextDomain());

            if (0 !== \strncmp($row['subject'], $subj, \strlen($subj)))
            {
                $row['subject'] = \LotgdTranslator::t('section.write.reply.subject', [
                    'subject' => $row['subject'],
                ], $this->getTextDomain());
            }
            $row['body'] = \sprintf(
                "\n\n---%s---\n%s",
                \LotgdTranslator::t('section.write.reply.body', [
                    'name' => \trim(\LotgdSanitize::fullSanitize($row['name'])),
                    'date' => $row['sent'],
                ], $this->getTextDomain()),
                $row['body']
            );

            $params['superusers'] = [];

            if (($row['acctid'] ?? false) && ($row['superuser'] & SU_GIVES_YOM_WARNING) && ! ($row['superuser'] & SU_OVERRIDE_YOM_WARNING))
            {
                $params['superusers'][] = $row['acctid'];
            }

            $params['row']   = $row;
            $params['msgId'] = $reply;

            $this->composer($params, $response);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->dialog->error(\LotgdTranslator::t('jaxon.fail.reply', [], $this->getTextDomain()));
        }

        return $response;
    }

    private function composer(&$params, &$response)
    {
        // Dialog content
        $content = $this->getTemplate()->renderBlock('mail_write', $params);

        // Dialog title
        $title = \LotgdTranslator::t('title', [], $this->getTextDomain());

        // The dialog buttons
        $buttons = [
            [
                'title' => \LotgdTranslator::t('section.write.form.button.submit', [], $this->getTextDomain()),
                'class' => 'ui approve primary button',
            ],
            [
                'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app_default'),
                'class' => 'ui red deny button',
            ],
        ];

        //-- Options
        $options = [
            'autofocus' => false,
            'onApprove' => "
                element.addClass('loading disabled');
                JaxonLotgd.Ajax.Core.Mail.send(jaxon.getFormValues('mail-message'));

                return false;
            ",
        ];

        $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons, $options);
    }
}
