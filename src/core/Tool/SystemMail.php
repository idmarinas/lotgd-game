<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 6.1.0
 */

namespace Lotgd\Core\Tool;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Entity\Mail;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Output\Format;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class SystemMail
{
    private $doctrine;
    private $translator;
    private $settings;
    private $request;
    private $sanitize;
    private $format;
    private $validator;
    private $mailer;

    public function __construct(
        EntityManagerInterface $doctrine,
        TranslatorInterface $translator,
        Settings $settings,
        Request $request,
        Sanitize $sanitize,
        Format $format,
        Validator $validator
    ) {
        $this->doctrine   = $doctrine;
        $this->translator = $translator;
        $this->settings   = $settings;
        $this->request    = $request;
        $this->sanitize   = $sanitize;
        $this->format     = $format;
        $this->validator  = $validator;
    }

    /**
     * Send a system mail (internal mail) to user.
     *
     * @param int          $to      ID of the user receiving the mail
     * @param string|array $subject Subject of mail (can be array for translation purpose)
     * @param string|array $body    Message of mail (can be array for translation purpose)
     * @param int          $from    ID of user that send the
     * @param bool         $noEmail Not send email when receiving mail
     */
    public function send(int $to, $subject, $body, int $from = 0, $noEmail = false): void
    {
        global $session;

        /** @var \Lotgd\Core\Repository\UserRepository $acctRepository */
        $acctRepository = $this->doctrine->getRepository('LotgdCore:User');

        /** @var \Lotgd\Core\Entity\User $accountEntityTo */
        $accountEntityTo = $acctRepository->find($to);
        $prefs           = $accountEntityTo->getPrefs();

        $entity = (new Mail())
            ->setMsgfrom($from)
            ->setMsgto($to)
            ->setSubject(\is_array($subject) ? serialize($subject) : str_replace(["\n", '`n'], '', $subject))
            ->setBody(\is_array($body) ? serialize($body) : $body)
            ->setSent(new DateTime('now'))
            ->setOriginator($session['user']['acctid'])
        ;

        $this->doctrine->persist($entity);
        $this->doctrine->flush();

        $email = (
            (isset($prefs['emailonmail']) && $prefs['emailonmail'] && $from > 0)
            || (isset($prefs['emailonmail']) && $prefs['emailonmail'] && 0 == $from && isset($prefs['systemmail']) && $prefs['systemmail'])
        );

        if ( ! $this->validator->isMail($accountEntityTo->getEmailaddress()))
        {
            $email = false;
        }

        if ($email && ! $noEmail)
        {
            $fromName = $acctRepository->getCharacterNameFromAcctId($from);
            $toName   = $acctRepository->getCharacterNameFromAcctId($to);

            $fromline = $this->sanitize->fullSanitize($fromName) ?: $this->settings->getSetting('servername');
            $toline   = $this->sanitize->fullSanitize($toName);

            // We've inserted it into the database, so.. strip out any formatting
            // codes from the actual email we send out... they make things
            // unreadable
            $body    = \is_array($body) ? $this->translator->trans($body[0], $body[1], $body[2], $prefs['language'] ?? null) : $body;
            $body    = $this->sanitize->fullSanitize(preg_replace('/`n/', "\n", $body));
            $subject = \is_array($subject) ? $this->translator->trans($subject[0], $subject[1], $subject[2], $prefs['language'] ?? null) : $subject;
            $subject = $this->sanitize->fullSanitize($subject);

            $mailSubject = $this->translator->trans('notificationmail.subject', [
                'subject' => $subject,
            ], 'app_mail');

            $mailMessage = $this->translator->trans('notificationmail.body', [
                'subject'      => $subject,
                'sendername'   => $fromline,
                'receivername' => $toline,
                'body'         => $body,
                'gameurl'      => $this->request->getServer('REQUEST_SCHEME').'://'.$this->request->getServer('SERVER_NAME'),
            ], 'app_mail');

            $email = (new Email())
                ->to($accountEntityTo->getEmailaddress())
                ->subject($this->sanitize->fullSanitize($mailSubject))
                ->html($this->format->colorize($mailMessage))
            ;

            $this->mailer->send($email);
        }
    }

    /**
     * Set mailer.
     */
    public function setMailer(MailerInterface $mailer): self
    {
        $this->mailer = $mailer;

        return $this;
    }
}
