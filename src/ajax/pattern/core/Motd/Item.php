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

use DateTime;
use Jaxon\Response\Response;
use Lotgd\Core\Entity\Motd as EntityMotd;
use Lotgd\Core\EntityForm\MotdEditType;
use Lotgd\Core\EntityForm\MotdType;
use Tracy\Debugger;

trait Item
{
    public function addItem(?array $post = null): Response
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

        try
        {
            // Dialog title
            $title = \LotgdTranslator::t('title', [], $this->getTextDomain());

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('item.button.submit', [], 'form-core-jaxon-motd'),
                    'class' => 'ui green approve button',
                ],
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
                    'class' => 'ui red deny button',
                ],
            ];

            $form = \LotgdForm::create(MotdType::class, new EntityMotd(), [
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
                    $entity = $form->getData();

                    $entity->setMotdtype(false);
                    $entity->setMotdauthor($session['user']['acctid']);

                    \Doctrine::persist($entity);
                    \Doctrine::flush();

                    $response = $this->list();

                    $response->dialog->success(\LotgdTranslator::t('item.add.item.success', [], $this->getTextDomain()));

                    return $response;
                }
            }

            $params['form'] = $form->createView();

            $content = \LotgdTheme::renderThemeTemplate('page/motd/add/item.twig', $params);

            $script = \stripslashes('<div class="ui active centered inline loader"></div>');
            //-- Options
            $options = [
                'autofocus' => false,
                'onApprove' => "
                    element.addClass('loading disabled');
                    JaxonLotgd.Ajax.Core.Motd.addItem(jaxon.getFormValues('{$form->getName()}'));
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

            $response->jQuery('.motd.item.button')->removeClass('loading disabled');
            $response->dialog->error(\LotgdTranslator::t('item.add.item.fail', [], $this->getTextDomain()));
        }

        return $response;
    }

    /**
     * Edit a MOTD item.
     *
     * @param mixed $post
     */
    public function editItem(int $id, ?array $post = null): Response
    {
        global $session;

        $response   = new Response();
        $repository = $this->getRepository();
        $params     = $this->getParams();
        $check      = $this->checkLoggedIn();
        $permission = checkSuPermission(SU_POST_MOTD);
        $entity     = $repository->find($id);

        if (true !== $check || ! $permission || ! $entity)
        {
            if ( ! $entity)
            {
                $response->dialog->error(\LotgdTranslator::t('item.edit.notFound', ['id' => $id], $this->getTextDomain()));
            }

            //-- Cant edit if not logged in and have SU_POST_MOTD
            return $response;
        }

        try
        {
            // Dialog title
            $title = \LotgdTranslator::t('title', [], $this->getTextDomain());

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('item.button.submit', [], 'form-core-jaxon-motd'),
                    'class' => 'ui green approve button',
                ],
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
                    'class' => 'ui red deny button',
                ],
            ];

            $form = \LotgdForm::create(MotdEditType::class, $entity, [
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
                    $entity = $form->getData();

                    $entity->setMotdtype(false);

                    if ($form->get('changedate')->getData())
                    {
                        $entity->setMotddate(new DateTime('now'));
                    }

                    if ($form->get('changeauthor')->getData())
                    {
                        $entity->setMotdauthor($session['user']['acctid']);
                    }

                    \Doctrine::persist($entity);
                    \Doctrine::flush();

                    $response = $this->list();

                    $response->dialog->success(\LotgdTranslator::t('item.edit.success', [], $this->getTextDomain()));

                    return $response;
                }
            }

            $params['form']     = $form->createView();
            $params['motdData'] = $repository->getEditMotdItem($id);

            $content = \LotgdTheme::renderThemeTemplate('page/motd/edit/item.twig', $params);

            $script = \stripslashes('<div class="ui active centered inline loader"></div>');
            //-- Options
            $options = [
                'autofocus' => false,
                'onApprove' => "
                    element.addClass('loading disabled');
                    JaxonLotgd.Ajax.Core.Motd.editItem({$id}, jaxon.getFormValues('{$form->getName()}'));
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

            $response->jQuery('.motd.item.button')->removeClass('loading disabled');
            $response->dialog->error(\LotgdTranslator::t('item.edit.item.fail', [], $this->getTextDomain()));
        }

        return $response;
    }
}
