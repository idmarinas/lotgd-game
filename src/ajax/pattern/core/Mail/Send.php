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
use Laminas\Filter;
use Tracy\Debugger;

trait Send
{
    /**
     * Send message.
     */
    public function send(array $post): Response
    {
        global $session;

        $check = $this->checkLoggedInRedirect();

        if (true !== $check)
        {
            return $check;
        }

        try
        {
            $response       = new Response();
            $from           = $session['user']['acctid'];
            $repository     = $this->getRepository();
            $repositoryAcct = $this->getAcctRepository();

            $account = $repositoryAcct->find($post['to']);
            $count   = $account ? $repository->countInboxOfCharacter($account->getAcctid(), (bool) \LotgdSetting::getSetting('onlyunreadmails', true)) : null;

            if ( ! $account || $count >= \LotgdSetting::getSetting('inboxlimit', 50) || (empty($post['subject']) || empty($post['body'])))
            {
                $message = $account ? 'jaxon.fail.send.inbox.full' : 'jaxon.fail.send.not.found';
                $message = (empty($post['subject']) || empty($post['body'])) ? 'jaxon.fail.send.subject.body' : $message;
                $response->dialog->warning(\LotgdTranslator::t($message, [], $this->getTextDomain()));

                $response->jQuery('.ui.approve.primary.button')->removeClass('loading disabled');

                return $response;
            }

            require_once 'lib/systemmail.php';

            $subject = $this->sanitize((string) $post['subject'], true);
            $body    = \substr($this->sanitize((string) $post['body'], false), 0, (int) \LotgdSetting::getSetting('mailsizelimit', 1024));

            systemmail($account->getAcctid(), $subject, $body, $from);

            $response = $this->inbox();

            $response->dialog->success(\LotgdTranslator::t('jaxon.success.send.mail.sent', [], $this->getTextDomain()));
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->dialog->error(\LotgdTranslator::t('jaxon.fail.send', [], $this->getTextDomain()));
        }

        return $response;
    }

    /**
     * Filter a string.
     */
    private function sanitize(string $string, bool $isSubject): string
    {
        $filterChain = new Filter\FilterChain();
        $filterChain
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags())
            ->attach(new Filter\StripNewlines(), -1)
        ;

        if ( ! $isSubject)
        {
            $filterChain
                ->attach(new Filter\PregReplace(['pattern' => '/\R/', 'replacement' => '`n']))
            ;
        }

        return $filterChain->filter($string);
    }
}
