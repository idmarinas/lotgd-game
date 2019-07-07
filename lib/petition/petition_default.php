<?php

if (\LotgdHttp::isPost())
{
    $post = \LotgdHttp::getPostAll();

    $count = $repository->getCountPetitionsForNetwork(\LotgdHttp::getServer('REMOTE_ADDR'), \LotgdHttp::getCookie('lgi'));

    if ($count >= 5 && (($session['user']['superuser'] ?? false) && $session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO))
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.section.default.error.network', ['count' => $count], $textDomain));

        return redirect('petition.php');
    }

    $session['user']['acctid'] = $session['user']['acctid'] ?? 0;
    $session['user']['password'] = $session['user']['password'] ?? '';

    $p = $session['user']['password'];
    unset($session['user']['password']);

    $post['cancelpetition'] = $post['cancelpetition'] ?? false;
    $post['cancelreason'] = $post['cancelreason'] ?? '' ?: \LotgdTranslator::t('section.default.post.cancel', [], $textDomain);

    $post = modulehook('addpetition', $post);

    if ($post['cancelpetition'])
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.section.default.error.cancel', [ 'reason' => $post['cancelreason'] ], $textDomain));

        return redirect('petition.php');
    }

    $entity = $repository->hydrateEntity([
        'author' => $session['user']['acctid'],
        'date' => new \DateTime('now'),
        'body' => $post,
        'pageinfo' => $session,
        'ip' => \LotgdHttp::getServer('REMOTE_ADDR'),
        'id' => \LotgdHttp::getCookie('lgi')
    ]);

    bdump($entity);

    \Doctrine::persist($entity);
    \Doctrine::flush();

    // Fix the counter
    invalidatedatacache('petitioncounts');
    // If the admin wants it, email the petitions to them.
    if (getsetting('emailpetitions', 0))
    {
        require_once 'lib/systemmail.php';

        // Yeah, the format of this is ugly.
        $name = \LotgdSanitize::fullSanitize($session['user']['name']);

        $url = getsetting('serverurl', \LotgdHttp::getServer('SERVER_NAME'));

        if (! preg_match('/\\/$/', $url))
        {
            $url = $url.'/';
            savesetting('serverurl', $url);
        }

        $tl_server = \LotgdTranslator::t('section.default.petition.mail.server', [] , $textDomain);
        $tl_author = \LotgdTranslator::t('section.default.petition.mail.author', [] , $textDomain);
        $tl_date = \LotgdTranslator::t('section.default.petition.mail.date', [] , $textDomain);
        $tl_body = \LotgdTranslator::t('section.default.petition.mail.body', [] , $textDomain);
        $tl_subject = \LotgdTranslator::t('section.default.petition.mail.subject', [ 'url' => \LotgdHttp::getServer('SERVER_NAME')], $textDomain);

        $msg = "$tl_server: $url\n";
        $msg .= "$tl_author: $name\n";
        $msg .= "$tl_date : $date\n";
        $msg .= "$tl_body :\n".\Zend\Debug\Debug::dump($post, 'Post', false)."\n";

        lotgd_mail(getsetting('gameadminemail', 'postmaster@localhost.com'), $tl_subject, $msg);
    }

    $session['user']['password'] = $p;

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.section.default.success.send', [], $textDomain));
}

$types = getsetting('petition_types', 'General');
$params['petition_types'] = explode(',', $types);
