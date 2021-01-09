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

namespace Lotgd\Ajax\Core;

use Jaxon\Response\Response;
use Lotgd\Ajax\Pattern\Core as PatternCore;
use Lotgd\Core\AjaxAbstract;
use Lotgd\Core\EntityRepository\AccountsRepository;
use Lotgd\Core\EntityRepository\MailRepository;

class Mail extends AjaxAbstract
{
    use PatternCore\Mail\Delete;
    use PatternCore\Mail\Inbox;
    use PatternCore\Mail\Read;
    use PatternCore\Mail\Send;
    use PatternCore\Mail\Write;

    public const TEXT_DOMAIN = 'jaxon_mail';

    protected $repositoryMail;
    protected $repositoryAcct;
    protected $templateMail;

    /**
     * Check status of inbox.
     */
    public function status(): Response
    {
        global $session;

        $check = $this->checkLoggedInRedirect();

        if (true !== $check)
        {
            return $check;
        }

        $response = new Response();
        $mail     = $this->getRepository();

        $result = $mail->getCountMailOfCharacter((int) ($session['user']['acctid'] ?? 0));

        $response->call('Lotgd.set', 'mailCount', [
            'new' => (int) $result['notSeenCount'],
            'old' => (int) $result['seenCount'],
        ]);

        $response->html('ye-olde-mail-count-text', \LotgdTranslator::t('parts.mail.title', [
            'new' => $result['notSeenCount'],
            'old' => $result['seenCount'],
        ], 'app_default'));

        return $response;
    }

    /**
     * Get text domain.
     */
    public function getTextDomain(): string
    {
        return self::TEXT_DOMAIN;
    }

    /**
     * Get repository of Mail entity.
     */
    protected function getRepository(): MailRepository
    {
        if ( ! $this->repositoryMail instanceof MailRepository)
        {
            $this->repositoryMail = \Doctrine::getRepository('LotgdCore:Mail');
        }

        return $this->repositoryMail;
    }

    /**
     * Get repository of Characters entity.
     */
    protected function getAcctRepository(): AccountsRepository
    {
        if ( ! $this->repositoryAcct instanceof AccountsRepository)
        {
            $this->repositoryAcct = \Doctrine::getRepository('LotgdCore:Accounts');
        }

        return $this->repositoryAcct;
    }

    /**
     * Get default params.
     */
    protected function getParams(): array
    {
        return [
            'textDomain'    => self::TEXT_DOMAIN,
            'mailSizeLimit' => getsetting('mailsizelimit', 1024),
        ];
    }

    /**
     * Get template block for Mail.
     */
    protected function getTemplate()
    {
        if ( ! $this->templateMail)
        {
            $this->templateMail = \LotgdTheme::load('{theme}/_blocks/_mail.html.twig');
        }

        return $this->templateMail;
    }
}
