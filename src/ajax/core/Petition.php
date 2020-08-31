<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Ajax\Core;

use Jaxon\Response\Response;
use Lotgd\Ajax\Pattern\Core as PatternCore;
use Lotgd\Core\AjaxAbstract;
use Lotgd\Core\EntityRepository\PetitionsRepository;
use Tracy\Debugger;

/**
 * Dialog for petition for help.
 */
class Petition extends AjaxAbstract
{
    use PatternCore\Petition\Faq;
    use PatternCore\Petition\Report;

    const TEXT_DOMAIN = 'jaxon-petition';
    protected $repositoryPetition;

    public function help(?array $post = null): Response
    {
        global $session;

        $response   = new Response();
        $repository = $this->getRepository();
        $params     = $this->getParams();

        try
        {
            $form      = \LotgdLocator::get('Lotgd\Core\Form\Petition');
            $formClone = clone $form;

            if ($post)
            {
                $form->setData($post);

                if ($form->isValid())
                {
                    $post  = $form->getData();
                    $count = $repository->getCountPetitionsForNetwork(\LotgdHttp::getServer('REMOTE_ADDR'), \LotgdHttp::getCookie('lgi'));

                    if ($count >= 5 || (($session['user']['superuser'] ?? false) && $session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO))
                    {
                        $response->dialog->warning(\LotgdTranslator::t('flash.message.section.default.error.network', ['count' => $count], self::TEXT_DOMAIN));

                        return $response;
                    }

                    $session['user']['acctid']   = $session['user']['acctid']   ?? 0;
                    $session['user']['password'] = $session['user']['password'] ?? '';

                    $p = $session['user']['password'];
                    unset($session['user']['password']);

                    $post['cancelpetition'] = $post['cancelpetition'] ?? false;
                    $post['cancelreason']   = $post['cancelreason']   ?? '' ?: \LotgdTranslator::t('section.default.post.cancel', [], self::TEXT_DOMAIN);

                    $post = modulehook('addpetition', $post);

                    if ($post['cancelpetition'])
                    {
                        $response->dialog->warning(\LotgdTranslator::t('flash.message.section.default.error.cancel', ['reason' => $post['cancelreason']], self::TEXT_DOMAIN));

                        return $response;
                    }

                    $entity = $repository->hydrateEntity([
                        'author'   => $session['user']['acctid'],
                        'date'     => new \DateTime('now'),
                        'body'     => $post,
                        'pageinfo' => $session,
                        'ip'       => \LotgdHttp::getServer('REMOTE_ADDR'),
                        'id'       => \LotgdHttp::getCookie('lgi'),
                    ]);

                    $session['user']['password'] = $p;

                    \Doctrine::persist($entity);
                    \Doctrine::flush();

                    // Fix the counter
                    \LotgdCache::removeItem('petitioncounts');

                    // If the admin wants it, email the petitions to them.
                    $this->emailPetitionAdmin($post['charname']);

                    $form = $formClone;

                    $response->dialog->success(\LotgdTranslator::t('flash.message.section.default.success.send', [], self::TEXT_DOMAIN));
                }
            }
            elseif ($session['user']['loggedin'] ?? false)
            {
                $form->setData([
                    'charname' => $session['user']['name'],
                    'email'    => $session['user']['emailaddress'],
                ]);
            }

            $params['form'] = $form;

            // Dialog content
            $content = \LotgdTheme::renderThemeTemplate('jaxon/petition/help.twig', $params);

            // Dialog title
            $title = \LotgdTranslator::t('title.default', [], self::TEXT_DOMAIN);

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('button.submit', [], 'form-core-jaxon-petition'),
                    'class' => 'ui green approve button',
                ],
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
                    'class' => 'ui red deny button',
                ],
            ];

            //-- Options
            $options = [
                'autofocus' => false,
                'onApprove' => "
                    element.addClass('loading disabled');
                    JaxonLotgd.Ajax.Core.Petition.help(jaxon.getFormValues('{$form->getName()}'));

                    return false;
                ",
            ];

            $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons, $options);
            $response->jQuery('.ui.lotgd.dropdown ')->dropdown();
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->dialog->error(\LotgdTranslator::t('jaxon.load.fail.help', [], self::TEXT_DOMAIN));
        }

        $response->jQuery('#petition-button')->removeClass('loading disabled');

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
     * Get repository of Petitions entity.
     */
    private function getRepository(): PetitionsRepository
    {
        if ( ! $this->repositoryPetition instanceof PetitionsRepository)
        {
            $this->repositoryPetition = \Doctrine::getRepository('LotgdCore:Petitions');
        }

        return $this->repositoryPetition;
    }

    /**
     * If the admin wants it, email the petitions to them.
     *
     * @param string $name
     * @return void
     */
    private function emailPetitionAdmin(string $name): void
    {

        if ( ! getsetting('emailpetitions', 0))
        {
            return;
        }

        require_once 'lib/systemmail.php';

        $url = getsetting('serverurl', \LotgdHttp::getServer('SERVER_NAME'));

        if ( ! \preg_match('/\\/$/', $url))
        {
            $url = $url.'/';
            savesetting('serverurl', $url);
        }

        $tl_server  = \LotgdTranslator::t('section.default.petition.mail.server', [], self::TEXT_DOMAIN);
        $tl_author  = \LotgdTranslator::t('section.default.petition.mail.author', [], self::TEXT_DOMAIN);
        $tl_date    = \LotgdTranslator::t('section.default.petition.mail.date', [], self::TEXT_DOMAIN);
        $tl_body    = \LotgdTranslator::t('section.default.petition.mail.body', [], self::TEXT_DOMAIN);
        $tl_subject = \LotgdTranslator::t('section.default.petition.mail.subject', ['url' => \LotgdHttp::getServer('SERVER_NAME')], self::TEXT_DOMAIN);

        $msg = "{$tl_server}: {$url}\n";
        $msg .= "{$tl_author}: {$name}\n";
        $msg .= "{$tl_date} : {$date}\n";
        $msg .= "{$tl_body} :\n".Debugger::dump($post, 'Post', false)."\n";

        lotgd_mail(getsetting('gameadminemail', 'postmaster@localhost.com'), $tl_subject, $msg);
    }

    /**
     * Get default params.
     */
    private function getParams(): array
    {
        return [
            'textDomain'  => self::TEXT_DOMAIN,
            'daysPerDay'  => getsetting('daysperday', 2),
            'multimaster' => (int) getsetting('multimaster', 1),
        ];
    }
}
