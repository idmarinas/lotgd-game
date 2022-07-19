<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 8.0.0
 */

namespace Lotgd\Core\Tool;

use Lotgd\Core\Kernel;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Tool\Sanitize;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

/**
 * Send an email.
 */
class LotgdMail
{
    private Sanitize $sanitize;
    private MailerInterface $mailer;
    private Settings $settings;
    private Environment $twig;

    private Email $email;

    public function __construct(Sanitize $sanitize, MailerInterface $mailer, Settings $settings, Environment $twig)
    {
        $this->sanitize = $sanitize;
        $this->mailer   = $mailer;
        $this->settings = $settings;
        $this->twig     = $twig;
    }

    /**
     * Configure email.
     */
    public function email(Address $to, string $subject, string $message): Email
    {
        $this->email = (new Email())
            ->to($to)
            ->subject($this->sanitize->fullSanitize($subject))
        ;

        if ($this->settings->getSetting('sendhtmlmail', 0) && $this->emailTemplateExist())
        {
            $data = [
                'title'     => $subject,
                'content'   => $message,
                'copyright' => Kernel::COPYRIGHT,
                'url'       => $this->settings->getSetting('serverurl', '//'.$_SERVER['SERVER_NAME']),
            ];

            $message = $this->twig->render('mail.twig', $data);

            $this->email->html($message);

            unset($data);
        }
        else
        {
            $this->email->text(nl2br($this->sanitize->fullSanitize($message)));
        }

        return $this->email;
    }

    /**
     * Reemplace email with this.
     */
    public function setEmail(Email $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Send email message.
     */
    public function send(): void
    {
        $this->mailer->send($this->email);
    }

    /**
     * Create email and send.
     */
    public function sendEmail(Address $to, string $subject, string $message)
    {
        $this->email($to, $subject, $message);
        $this->send();
    }

    private function emailTemplateExist(): bool
    {
        try
        {
            $this->twig->load('mail.twig');

            return true;
        }
        catch (\Throwable $th)
        {
            return false;
        }
    }
}
