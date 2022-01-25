<?php

/**
 * This file is part of "LoTGD Core Package".
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\Controller;

use Throwable;
use Laminas\Filter\FilterChain;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\StripNewlines;
use Laminas\Filter\PregReplace;
use Laminas\Filter;
use Lotgd\Core\Form\MailWriteType;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Pattern\LotgdControllerTrait;
use Lotgd\Core\Repository\AvatarRepository;
use Lotgd\Core\Repository\MailRepository;
use Lotgd\Core\Tool\Sanitize;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tracy\Debugger;

class MailController extends AbstractController implements LotgdControllerInterface
{
    use LotgdControllerTrait;

    public const TRANSLATION_DOMAIN = 'jaxon_mail';

    private $repository;
    private $settings;
    private $translator;
    private $avatarRepository;
    private $sanitize;

    public function __construct(
        MailRepository $repository,
        Settings $settings,
        TranslatorInterface $translator,
        AvatarRepository $avatarRepository,
        Sanitize $sanitize
    ) {
        $this->repository       = $repository;
        $this->settings         = $settings;
        $this->translator       = $translator;
        $this->avatarRepository = $avatarRepository;
        $this->sanitize         = $sanitize;
    }

    public function inbox(Request $request): Response
    {
        global $session;

        $params        = $this->getParams();
        $sortOrder     = (string) ($request->query->get('sort_order', 'date') ?: 'date');
        $sortDirection = $request->query->getInt('sort_direction');

        $params['inbox_limit']    = $this->settings->getSetting('inboxlimit', 50);
        $params['old_mail']       = $this->settings->getSetting('oldmail', 14);
        $params['new_direction']  = (int) ! $sortDirection;
        $params['sort_direction'] = $sortDirection;
        $params['sort_order']     = $sortOrder;

        $params['mails'] = $this->repository->getCharacterMail($session['user']['acctid'], $sortOrder, $sortDirection);
        // // $params['senders'] = $this->repository->getMailSenderNames($session['user']['acctid']);

        $params['inboxCount'] = \count($params['mails']);

        return $this->render('mail/inbox.html.twig', $params);
    }

    /**
     * Delete mail by ID.
     */
    public function delete(Request $request): Response
    {
        global $session;

        $id = $request->query->getInt('id');

        $delete = $this->repository->findOneBy([
            'messageid' => $id,
            'msgto'     => $session['user']['acctid'], //-- For not deleted message of other user
        ]);

        $type    = 'error';
        $message = $this->translator->trans('dialog.del.one.error', [], self::TRANSLATION_DOMAIN);

        if ($delete)
        {
            $this->getDoctrine()->getManager()->remove($delete);
            $this->getDoctrine()->getManager()->flush();

            $type    = 'success';
            $message = $this->translator->trans('dialog.del.one.success', [], self::TRANSLATION_DOMAIN);
        }

        $this->addNotification($type, $message);

        return $this->inbox($request);
    }

    /**
     * Delete mail in bulk by ID.
     */
    public function deleteBulk(Request $request): Response
    {
        global $session;

        $post = (array) $request->request->get('msg');

        $count = $this->repository->deleteBulkMail($post, (int) $session['user']['acctid']);

        $type    = 'error';
        $message = $this->translator->trans('dialog.del.bulk.error', [], self::TRANSLATION_DOMAIN);

        if (empty($post))
        {
            $message = $this->translator->trans('dialog.del.bulk.empty', [], self::TRANSLATION_DOMAIN);
        }
        elseif ($count !== 0)
        {
            $type    = 'success';
            $message = $this->translator->trans('dialog.del.bulk.success', [], self::TRANSLATION_DOMAIN);
        }

        $this->addNotification($type, $message);

        return $this->inbox($request);
    }

    /**
     * Undocumented function.
     */
    public function searchAvatar(Request $request): JsonResponse
    {
        $search = (string) $request->query->get('q', '');

        if ( $search === '' || $search === '0')
        {
            return $this->json([]);
        }

        $result = $this->avatarRepository->findLikeName($search, 15);

        $characters = [];

        foreach ($result as $char)
        {
            $superuser = ($char['superuser'] & SU_GIVES_YOM_WARNING) && ! ($char['superuser'] & SU_OVERRIDE_YOM_WARNING);

            $characters[] = [
                'value'     => $char['acctid'],
                'icon'      => ($char['loggedin'] ? 'text-green-300' : 'text-red-300').' '.($superuser ? 'fas fa-user-secret' : 'far fa-user'),
                'display'   => $this->sanitize->fullSanitize($char['name']),
                'superuser' => $superuser,
            ];
        }

        return $this->json($characters);
    }

    /**
     * Status of mail show in button.
     */
    public function status(): Response
    {
        global $session;

        $mail   = $this->getDoctrine()->getRepository('LotgdCore:Mail');
        $result = $mail->getCountMailOfCharacter((int) ($session['user']['acctid'] ?? 0));

        $params                   = $this->getParams();
        $params['not_seen_count'] = $result['not_seen_count'];
        $params['seen_count']     = $result['seen_count'];

        return $this->render('mail/status.html.twig', $params);
    }

    /**
     * Write message for a user.
     */
    public function write(Request $request): Response
    {
        $toPlayer = $request->query->getInt('to_player');

        $params = $this->getParams();

        $form = $this->createForm(MailWriteType::class);

        if ($toPlayer !== 0)
        {
            /** @var Lotgd\Core\Entity\User $account */
            $account = $this->getDoctrine()->getRepository('LotgdCore:User')->find($toPlayer);

            if ($account)
            {
                $form->setData([
                    'to'   => $account->getAcctid(),
                    'name' => $account->getCharacter()->getName(),
                ]);
            }
        }

        $formEmpty = clone $form;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->send($form, $formEmpty);

            $isSubmitted = true;
        }

        $params['form'] = $form->createView();

        $isSubmitted = $isSubmitted ?? $form->isSubmitted();

        return $this->renderBlock('mail/write.html.twig', $isSubmitted ? 'content' : 'dialog', $params);
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request): Response
    {
        global $session;

        try
        {
            $messageId = $request->query->getInt('message_id');
            $params    = $this->getParams();

            $form = $this->createForm(MailWriteType::class);
            $row  = $this->repository->replyToMessage($messageId, $session['user']['acctid']);

            //-- Not found message to reply
            if ( $row === [])
            {
                $this->addNotification('error', $this->translator->trans('jaxon.fail.message.not.found', [], self::TRANSLATION_DOMAIN));

                $isSubmitted = $isSubmitted ?? $form->isSubmitted();

                $params['form'] = $form->createView();

                return $this->renderBlock('mail/write.html.twig', $isSubmitted ? 'content' : 'dialog', $params);
            }

            $subj = $this->translator->trans('section.write.reply.subject', [
                'subject' => '',
            ], self::TRANSLATION_DOMAIN);

            if (0 !== strncmp($row['subject'], $subj, \strlen($subj)))
            {
                $row['subject'] = $this->translator->trans('section.write.reply.subject', [
                    'subject' => $row['subject'],
                ], self::TRANSLATION_DOMAIN);
            }

            $row['body'] = sprintf(
                "\n\n---%s---\n%s",
                $this->translator->trans('section.write.reply.body', [
                    'name' => trim($this->sanitize->fullSanitize($row['name'])),
                    'date' => $row['sent'],
                ], self::TRANSLATION_DOMAIN),
                $row['body']
            );

            $params['superusers'] = [];

            if (($row['acctid'] ?? false) && ($row['superuser'] & SU_GIVES_YOM_WARNING) && ! ($row['superuser'] & SU_OVERRIDE_YOM_WARNING))
            {
                $params['superusers'][] = $row['acctid'];
            }

            $params['row']   = $row;
            $params['msgId'] = $messageId;

            $formEmpty = clone $form;
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $this->send($form, $formEmpty);

                $isSubmitted = true;
            }
            else
            {
                $form->setData($row);
            }
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            $this->addNotification('error', $this->translator->trans('jaxon.fail.reply', [], self::TRANSLATION_DOMAIN));
        }

        $isSubmitted = $isSubmitted ?? $form->isSubmitted();

        $params['form'] = $form->createView();

        return $this->renderBlock('mail/write.html.twig', $isSubmitted ? 'content' : 'dialog', $params);
    }

    public function read(Request $request): Response
    {
        global $session;

        $id = $request->query->getInt('message_id');

        $params = $this->getParams();

        /** @var \Lotgd\Core\Entity\Mail $message */
        $message = $this->repository->findOneBy([
            'messageid' => $id,
            'msgto'     => $session['user']['acctid'],
        ]);

        if ( ! $message)
        {
            $this->addNotification('error', $this->translator->trans('jaxon.fail.read.not.found', [], self::TRANSLATION_DOMAIN));

            return $this->inbox($request);
        }

        /** @var \Lotgd\Core\Repository\Avatar $charRepository */
        $charRepository = $this->getDoctrine()->getRepository('LotgdCore:Avatar');

        $params['message']    = clone $message;
        $params['sender']     = $charRepository->findOneByAcct($message->getMsgfrom());
        $params['pagination'] = $this->repository->getNextPreviousMail($message->getMessageid(), $session['user']['acctid']);

        $message->setSeen(true);

        $this->getDoctrine()->getManager()->persist($message);
        $this->getDoctrine()->getManager()->flush();

        return $this->renderBlock('mail/read.html.twig', 'dialog', $params);
    }

    /**
     * Mark message as unread.
     */
    public function unread(Request $request): Response
    {
        global $session;

        $id = $request->query->getInt('message_id');

        /** @var \Lotgd\Core\Entity\Mail $message */
        $message = $this->repository->findOneBy([
            'messageid' => $id,
            'msgto'     => $session['user']['acctid'],
        ]);

        if ( ! $message)
        {
            $this->addNotification('error', $this->translator->trans('jaxon.fail.unread.not.found', [], self::TRANSLATION_DOMAIN));

            return $this->inbox($request);
        }

        //-- Mark as unread
        $message->setSeen(false);

        $this->getDoctrine()->getManager()->persist($message);
        $this->getDoctrine()->getManager()->flush();

        $this->addNotification('success', $this->translator->trans('jaxon.success.unread', [], self::TRANSLATION_DOMAIN));

        return $this->inbox($request);
    }

    /**
     * Not allow anonymous access.
     */
    public function allowAnonymous(): bool
    {
        return false;
    }

    /**
     * Override navs.
     */
    public function overrideForcedNav(): bool
    {
        return true;
    }

    private function send(FormInterface &$form, FormInterface $formEmpty): void
    {
        global $session;

        $post = $form->getData();

        try
        {
            $from           = $session['user']['acctid'];
            $repositoryAcct = $this->getDoctrine()->getRepository('LotgdCore:User');

            $account = $repositoryAcct->find($post['to']);
            $count   = $account !== null ? $this->repository->countInboxOfCharacter($account->getAcctid(), (bool) $this->settings->getSetting('onlyunreadmails', true)) : null;

            if ( ! $account || $count >= $this->settings->getSetting('inboxlimit', 50) || (empty($post['subject']) || empty($post['body'])))
            {
                $message = $account !== null ? 'jaxon.fail.send.inbox.full' : 'jaxon.fail.send.not.found';
                $message = (empty($post['subject']) || empty($post['body'])) ? 'jaxon.fail.send.subject.body' : $message;

                $this->addNotification('warning', $this->translator->trans($message, [], self::TRANSLATION_DOMAIN));

                return;
            }

            $subject = $this->sanitize((string) $post['subject'], true);
            $body    = substr($this->sanitize((string) $post['body'], false), 0, (int) $this->settings->getSetting('mailsizelimit', 1024));

            systemmail($account->getAcctid(), $subject, $body, $from);

            $this->addNotification('success', $this->translator->trans('jaxon.success.send.mail.sent', [], self::TRANSLATION_DOMAIN));

            $form = $formEmpty;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            $this->addNotification('error', $this->translator->trans('jaxon.fail.send', [], self::TRANSLATION_DOMAIN));
        }
    }

    /**
     * Filter a string.
     */
    private function sanitize(string $string, bool $isSubject): string
    {
        $filterChain = new FilterChain();
        $filterChain
            ->attach(new StringTrim())
            ->attach(new StripTags())
            ->attach(new StripNewlines(), -1)
        ;

        if ( ! $isSubject)
        {
            $filterChain
                ->attach(new PregReplace(['pattern' => '/\R/', 'replacement' => '`n']))
            ;
        }

        return $filterChain->filter($string);
    }

    /**
     * Get default params.
     */
    private function getParams(): array
    {
        return [
            'text_domain'     => self::TRANSLATION_DOMAIN,
            'mail_size_limit' => $this->settings->getSetting('mailsizelimit', 1024),
        ];
    }
}
