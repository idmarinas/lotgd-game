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

namespace Lotgd\Ajax\Core;

use Jaxon\Response\Response;
use Lotgd\Ajax\Pattern\Core as PatternCore;
use Lotgd\Core\AjaxAbstract;
use Lotgd\Core\Repository\PetitionsRepository;
use Lotgd\Core\Event\Core;
use Lotgd\Core\Form\PetitionType;
use Tracy\Debugger;

/**
 * Dialog for petition for help.
 */
class Petition extends AjaxAbstract
{
    use PatternCore\Petition\Faq;
    use PatternCore\Petition\Report;

    public const TEXT_DOMAIN = 'jaxon_petition';
    protected $repositoryPetition;
    protected $templatePetition;

    public function help(?array $post = null): Response
    {
        global $session;

        $response   = new Response();
        $repository = $this->getRepository();
        $params     = $this->getParams();

        try
        {
            $lotgdFormFactory = \LotgdKernel::get('form.factory');

            $form = $lotgdFormFactory->create(PetitionType::class, null, [
                'action' => '',
                'attr'   => [
                    'autocomplete' => 'off',
                ],
            ]);

            $formClone = clone $form;

            if ($post)
            {
                $form->submit($post[$form->getName()]);

                if ($form->isSubmitted() && $form->isValid())
                {
                    $result = $this->processForm($form, $formClone, $repository, $response);

                    if ($result)
                    {
                        return $result;
                    }
                }
            }
            elseif ($session['user']['loggedin'] ?? false)
            {
                $form->setData([
                    'charname' => $session['user']['name'],
                    'email'    => $session['user']['emailaddress'],
                ]);
            }

            $params['form'] = $form->createView();

            // Dialog content
            $content = $this->getTemplate()->renderBlock('petition_help', $params);

            // Dialog title
            $title = \LotgdTranslator::t('title.default', [], self::TEXT_DOMAIN);

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdKernel::get('translator')->trans('button.submit', [], 'form_core_petition'),
                    'class' => 'ui green approve button',
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
     * Get template of block for Petition.
     */
    protected function getTemplate()
    {
        if ( ! $this->templatePetition)
        {
            $this->templatePetition = \LotgdTheme::load('_blocks/_petition.html.twig');
        }

        return $this->templatePetition;
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
     */
    private function emailPetitionAdmin(string $name, array $post): void
    {
        if ( ! \LotgdSetting::getSetting('emailpetitions', 0))
        {
            return;
        }

        $date = \date('Y-m-d H:i:s');
        $url  = \LotgdSetting::getSetting('serverurl', \LotgdRequest::getServer('SERVER_NAME'));

        if ( ! \preg_match('/\\/$/', $url))
        {
            $url .= '/';
            \LotgdSetting::saveSetting('serverurl', $url);
        }

        $tlServer  = \LotgdTranslator::t('section.default.petition.mail.server', [], self::TEXT_DOMAIN);
        $tlAuthor  = \LotgdTranslator::t('section.default.petition.mail.author', [], self::TEXT_DOMAIN);
        $tlDate    = \LotgdTranslator::t('section.default.petition.mail.date', [], self::TEXT_DOMAIN);
        $tlBody    = \LotgdTranslator::t('section.default.petition.mail.body', [], self::TEXT_DOMAIN);
        $tlSubject = \LotgdTranslator::t('section.default.petition.mail.subject', ['url' => \LotgdRequest::getServer('SERVER_NAME')], self::TEXT_DOMAIN);

        $msg = "{$tlServer}: {$url}\n";
        $msg .= "{$tlAuthor}: {$name}\n";
        $msg .= "{$tlDate} : {$date}\n";
        $msg .= "{$tlBody} :\n".Debugger::dump($post, 'Post')."\n";

        lotgd_mail(\LotgdSetting::getSetting('gameadminemail', 'postmaster@localhost.com'), $tlSubject, $msg);
    }

    /**
     * Get default params.
     */
    private function getParams(): array
    {
        return [
            'textDomain'  => self::TEXT_DOMAIN,
            'daysPerDay'  => \LotgdSetting::getSetting('daysperday', 2),
            'multimaster' => (int) \LotgdSetting::getSetting('multimaster', 1),
        ];
    }

    private function processForm(&$form, $formClone, $repository, $response)
    {
        global $session;

        $post  = $form->getData();
        $count = $repository->getCountPetitionsForNetwork(\LotgdRequest::getServer('REMOTE_ADDR'), \LotgdRequest::getCookie('lgi'));

        if ($count >= 5 && ! (isset($session['user']['superuser']) && $session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO))
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

        $post = new Core($post);
        \LotgdEventDispatcher::dispatch($post, Core::PETITION_ADD);
        $post = modulehook('addpetition', $post->getData());

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
            'ip'       => \LotgdRequest::getServer('REMOTE_ADDR'),
            'id'       => \LotgdRequest::getCookie('lgi'),
        ]);

        $session['user']['password'] = $p;

        \Doctrine::persist($entity);
        \Doctrine::flush();

        // If the admin wants it, email the petitions to them.
        $this->emailPetitionAdmin($post['charname'], $post);

        $form = $formClone;

        $response->dialog->success(\LotgdTranslator::t('flash.message.section.default.success.send', [], self::TEXT_DOMAIN));
    }
}
