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

namespace Lotgd\Ajax\Pattern\Core\Petition;

use Jaxon\Response\Response;
use Tracy\Debugger;

trait Report
{
    public function report(int $playerId, string $message, ?array $post = null): Response
    {
        global $session;

        $response   = new Response();
        $params     = $this->getParams();
        $check      = $this->checkLoggedIn();
        $repository = $this->getRepository();

        if (true !== $check)
        {
            //-- Cant add if not logged in and have SU_POST_MOTD
            return $check;
        }

        try
        {
            $form = \LotgdLocator::get('Lotgd\Core\Form\Petition');
            $form->remove('problem_type');
            $inputFilter = $form->getInputFilter();
            $inputFilter->remove('problem_type');
            $form->setInputFilter($inputFilter);

            $formClone = clone $form;

            if ($post)
            {
                $form->setData($post);

                if ($form->isValid())
                {
                    $post = $form->getData();
                    $post['playerAbuseId'] = $playerId;
                    $post['abuseMessage'] = $message;

                    $p = $session['user']['password'];
                    unset($session['user']['password']);

                    $entity = $repository->hydrateEntity([
                        'author' => $session['user']['acctid'],
                        'date' => new \DateTime('now'),
                        'body' => $post,
                        'pageinfo' => $session,
                        'ip' => \LotgdHttp::getServer('REMOTE_ADDR'),
                        'id' => \LotgdHttp::getCookie('lgi')
                    ]);

                    $session['user']['password'] = $p;

                    \Doctrine::persist($entity);
                    \Doctrine::flush();

                    // Fix the counter
                    \LotgdCache::removeItem('petitioncounts');

                    // If the admin wants it, email the petitions to them.
                    $this->emailPetitionAdmin($post['charname']);

                    $form = $formClone;

                    $response->dialog->success(\LotgdTranslator::t('flash.message.section.default.success.send', [], $this->getTextDomain()));
                }
            }
            else
            {
                $form->setData([
                    'charname' => $session['user']['name'],
                    'email'    => $session['user']['emailaddress'],
                ]);
            }

            $params['form'] = $form;

            // Dialog content
            $content = \LotgdTheme::renderThemeTemplate('jaxon/petition/report.twig', $params);

            // Dialog title
            $title = \LotgdTranslator::t('title.report', [], $this->getTextDomain());

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('button.report', [], 'form-core-jaxon-petition'),
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
                    JaxonLotgd.Ajax.Core.Petition.report('{$playerId}', '{$message}', jaxon.getFormValues('{$form->getName()}'));

                    return false;
                ",
            ];

            $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons, $options);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->dialog->error(\LotgdTranslator::t('flash.message.error', [], 'app-default'));
            $response->jQuery('.ui.report.button')->removeClass('loading disabled');
        }

        return $response;
    }
}
