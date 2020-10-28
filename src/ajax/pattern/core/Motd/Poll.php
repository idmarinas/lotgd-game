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

namespace Lotgd\Ajax\Pattern\Core\Motd;

use Jaxon\Response\Response;
use Lotgd\Core\Entity\Motd as EntityMotd;
use Lotgd\Core\Entity\Pollresults;
use Lotgd\Core\EntityForm\MotdPollType;
use Tracy\Debugger;

trait Poll
{
    public function addPoll(?array $post = null): Response
    {
        global $session;

        $response   = new Response();
        $params     = $this->getParams();
        $check      = $this->checkLoggedIn();
        $permission = checkSuPermission(SU_POST_MOTD);

        if (true !== $check || ! $permission)
        {
            //-- Cant add if not logged in and have SU_POST_MOTD
            return $response;
        }

        // Dialog content
        try
        {
            // Dialog title
            $title = \LotgdTranslator::t('title', [], $this->getTextDomain());

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('poll.button.submit', [], 'form-core-jaxon-motd'),
                    'class' => 'ui green approve button',
                ],
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
                    'class' => 'ui red deny button',
                ],
            ];

            $form = \LotgdForm::create(MotdPollType::class, new EntityMotd(), [
                'action' => '',
                'attr'   => [
                    'autocomplete' => 'off',
                ],
            ]);

            if ($post)
            {
                $form->submit($post[$form->getName()]);

                if ($form->isSubmitted() && $form->isValid())
                {
                    $entity  = $form->getData();
                    $choices = $form->get('opt')->getData();
                    $body    = \serialize(['body' => $entity->getMotdbody(), 'opt' => $choices]);

                    $entity->setMotdbody($body);
                    $entity->setMotdtype(true);
                    $entity->setMotdauthor($session['user']['acctid']);

                    \Doctrine::persist($entity);
                    \Doctrine::flush();

                    $response = $this->list();

                    $response->dialog->success(\LotgdTranslator::t('item.add.poll.success', [], $this->getTextDomain()));

                    return $response;
                }
            }

            $params['form'] = $form->createView();

            $content = $this->getTemplate()->renderBlock('motd_poll_add', $params);

            $script = \stripslashes('<div class="ui active centered inline loader"></div>');
            //-- Options
            $options = [
                'autofocus' => false,
                'onApprove' => "
                    element.addClass('loading disabled');
                    JaxonLotgd.Ajax.Core.Motd.addPoll(jaxon.getFormValues('{$form->getName()}'));
                    element.parent('.actions').parent('.ui.modal').children('.content').html('{$script}');

                    return false;
                ",
            ];

            // Show the dialog
            $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons, $options);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->jQuery('.motd.poll.button')->removeClass('loading disabled');
            $response->dialog->error(\LotgdTranslator::t('item.add.poll.fail', [], $this->getTextDomain()));
        }

        return $response;
    }

    /**
     * Vote in a poll.
     */
    public function vote(int $id, int $choice): Response
    {
        global $session;

        $check = $this->checkLoggedIn();

        if (true !== $check)
        {
            //-- Cant vote if not logged in
            return $check;
        }

        try
        {
            $pollResult = new Pollresults();

            $pollResult->setChoice($choice)
                ->setAccount($session['user']['acctid'])
                ->setMotditem($id)
            ;

            \Doctrine::persist($pollResult);
            \Doctrine::flush();

            $type    = 'success';
            $message = \LotgdTranslator::t('item.poll.voting.success', [], $this->getTextDomain());
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $type    = 'error';
            $message = \LotgdTranslator::t('item.poll.voting.fail', [], $this->getTextDomain());
        }

        $response = $this->list();

        $response->dialog->{$type}($message);

        \LotgdCache::removeItem("poll-{$id}");

        return $response;
    }
}
