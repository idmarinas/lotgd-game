<?php

define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

tlschema('mail');

// Don't hook on to this text for your standard modules please, use "mail" instead.
// This hook is specifically to allow modules that do other mails to create ambience.
$result = modulehook('mail-text-domain', ['textDomain' => 'popup-mail']);
$textDomain = $result['textDomain'];
//-- Note: this page not have nav menu
unset($result);

$args = modulehook('header-mail', []);

$op = (string) \LotgdHttp::getQuery('op');
$mailId = (int) \LotgdHttp::getQuery('mail_id');

$repository = \Doctrine::getRepository('LotgdCore:Mail');

if ('unread' == $op)
{
    $unread = $repository->findOneBy([
        'messageid' => $mailId,
        'msgto' => $session['user']['acctid']
    ]);

    if ($unread)
    {
        //-- Mark as unread
        $unread->setSeen(false);

        \Doctrine::persist($unread);
        \Doctrine::flush();

        invalidatedatacache("mail-{$session['user']['acctid']}");
    }

    $op = '';
    \LotgdHttp::setQuery('op', null);
}

popup_header('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
    'mailButtons' => [
        [
            'button.inbox', //-- Translator key
            [
                'attributes' => [
                    'class' => '', //-- Additional class names
                    'href' => 'mail.php' //-- Link url
                ]
            ], //-- Params
            $textDomain //-- Text domain
        ],
        [
            'button.write',
            [
                'attributes' => [
                    'class' => 'primary',
                    'href' => 'mail.php?op=address'
                ]
            ],
            $textDomain
        ]
    ]
];

switch ($op)
{
    case 'address':
        $params['tpl'] = 'address';
        $params['mailId'] = $mailId;
    break;
    case 'read':
        $params['tpl'] = 'read';
        $params['message'] = null;

        $message = $repository->findOneBy([
            'messageid' => $mailId,
            'msgto' => $session['user']['acctid']
        ]);

        if ($message)
        {
            $charRepository = \Doctrine::getRepository('LotgdCore:Characters');

            $params['message'] = clone $message;
            $params['sender'] = $charRepository->findOneByAcct($message->getMsgfrom());

            $params['paginator'] = $repository->getNextPreviousMail($message->getMessageid(), $session['user']['acctid']);

            //-- Mark as read
            $message->setSeen(true);

            \Doctrine::persist($message);
            \Doctrine::flush();
        }
    break;
    case 'write':
        $params['tpl'] = 'write';

        $charRepository = \Doctrine::getRepository('LotgdCore:Characters');
        $replyTo = (int) \LotgdHttp::getQuery('reply_to');
        $forwardto = (int) (string) \LotgdHttp::getPost('forwardto');
        $to = (int) \LotgdHttp::getQuery('to');

        $subject = (string) \LotgdHttp::getPost('subject');

        $msgId = $replyTo ?: $forwardTo;

        //-- Get message to reply
        if ($msgId)
        {
            $row = $repository->replyToMessage($replyTo, $session['user']['acctid']);
        }

        //-- Get a list of recipents
        $countRecipents = null;

        if ($to)
        {
            $row = $charRepository->findOne($to);

            $countRecipents = count($row);
        }

        if (is_array($row) && ! $countRecipents)
        {
            $subj = \LotgdTranslator::t('section.write.reply.subject', [
                'subject' => ''
            ], $textDomain);

            if (0 !== strncmp($row['subject'], $subj, strlen($subj)))
            {
                $row['subject'] = \LotgdTranslator::t('section.write.reply.subject', [
                    'subject' => $row['subject']
                ], $textDomain);
            }
            $row['body'] = sprintf("\n\n---%s---\n%s",
                \LotgdTranslator::t('section.write.reply.body', [
                    'name' => trim(\LotgdSanitize::fullSanitize($row['name'])),
                    'date' => $row['sent']
                ], $textDomain),
                $row['body']
            );
        }

        $params['superusers'] = [];

        if (($row['acctid'] ?? false) && $countRecipents <= 1 && ($row['superuser'] & SU_GIVES_YOM_WARNING) && ! ($row['superuser'] & SU_OVERRIDE_YOM_WARNING))
        {
            array_push($params['superusers'], $row['acctid']);
        }
        else
        {
            $to = (string) \LotgdHttp::getPost('to');

            $characters = $charRepository->findLikeName($to);

            if (count($characters))
            {
                $params['superusers'] = [];

                foreach ($characters as $char)
                {
                    if (($char['superuser'] & SU_GIVES_YOM_WARNING) && ! ($char['superuser'] & SU_OVERRIDE_YOM_WARNING))
                    {
                        array_push($params['superusers'], $char['acctid']);
                    }
                }
            }

            $params['characters'] = $characters;
        }

        $params['row'] = $row;
        $params['msgId'] = $msgId;
        $params['mailSizeLimit'] = getsetting('mailsizelimit', 1024);
    break;
    case 'send':
        $to = (int) \LotgdHttp::getPost('to');
        $from = $session['user']['acctid'];
        $acctRepository = \Doctrine::getRepository('LotgdCore:Accounts');

        $account = $acctRepository->find($to);

        $params['message'][] = ['section.send.not.found'];

        if ($account)
        {
            $count = $repository->countInboxOfCharacter($account->getAcctid(), (bool) getsetting('onlyunreadmails', true));

            $params['message'] = [
                ['section.send.inbox.full']
            ];

            if ($count < getsetting('inboxlimit', 50))
            {
                require_once 'lib/systemmail.php';

                $params['message'] = [
                    ['section.send.mail.sent']
                ];

                $subject = str_replace('`n', '', (string) \LotgdHttp::getPost('subject'));
                $body = substr(stripslashes((string) \LotgdHttp::getPost('body')), 0, (int) getsetting('mailsizelimit', 1024));
                $body = str_replace(["\r\n", "\r"], '`n', $body);

                systemmail($account->getAcctid(), $subject, $body, $from);

                invalidatedatacache("mail-{$account->getAcctid()}");
            }
        }
    default:
        $params['tpl'] = 'default';

        $params['inboxLimit'] = getsetting('inboxlimit', 50);
        $params['oldMail'] = getsetting('oldmail', 14);

        $sortOrder = (string) \LotgdHttp::getQuery('sortorder') ?: 'date';
        $sortDirection = (int) \LotgdHttp::getQuery('direction');

        $params['newDirection'] = (int) ! $sortDirection;
        $params['sortDirection'] = $sortDirection;
        $params['sortOrder'] = $sortOrder;

        $params['mails'] = $repository->getCharacterMail($session['user']['acctid'], $sortOrder, $sortDirection);
        $params['senders'] = $repository->getMailSenderNames($session['user']['acctid']);

        $params['inboxCount'] = count($params['mails']);
    break;
}

//-- This is only for params not use for other purpose
$params = modulehook('popup-mail-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('popup/mail.twig', $params));

popup_footer();
