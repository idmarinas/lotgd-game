<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Controller;

use Lotgd\Bundle\AdminBundle\Event\DonationManualEvent;
use Lotgd\Bundle\CoreBundle\Form\DonationManualType;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\Form\FormRenderer;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DonatorAdminController extends CRUDController
{
    public function addManualAction(EventDispatcherInterface $dispatcher)
    {
        $request = $this->getRequest();

        $this->assertObjectExists($request);

        $newObject = $this->admin->getNewInstance();
        $this->admin->setSubject($newObject);

        $form = $this->createForm(DonationManualType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && ($form->isValid() && ( ! $this->isInPreviewMode() || $this->isPreviewApproved())))
        {
            $submittedObject = $form->getData();
            $this->admin->checkAccess('create');

            try
            {
                /** @var \Lotgd\Bundle\UserBundle\Entity\User */
                $userEntity = $submittedObject['user'];
                $reason = $this->trans('form.donator.reason_value', [], 'lotgd_core_admin');

                //-- Update Paylog registry
                if (\is_object($submittedObject['txnid']))
                {
                    /** @var \Lotgd\Bundle\CoreBundle\Entity\Paylog */
                    $paylog = $submittedObject['txnid'];
                    $paylog->setUser($userEntity)
                        ->setName($userEntity->getUsername())
                        ->setProcessed(true)
                        ->setProcessdate(new \DateTime('now'))
                    ;
                    $submittedObject['points'] = \floor($paylog->getAmount() * $this->getParameter('lotgd_bundle.donation.points_per_currency_unit'));
                    $reason = $this->trans('form.donator.reason_value_txnid', [], 'lotgd_core_admin');
                }

                $event = new DonationManualEvent($submittedObject);
                $dispatcher->dispatch($event, DonationManualEvent::PRE);

                $userEntity->setDonation($userEntity->getDonation() + $event->getPoints());
                $newObject = $this->admin->update($userEntity);


                if ($this->isXmlHttpRequest())
                {
                    return $this->handleXmlHttpRequestSuccessResponse($request, $newObject);
                }

                $this->addFlash(
                    'sonata_flash_success',
                    $this->trans(
                        'flash.donator.added_success',
                        [
                            'name'   => $this->escapeHtml($this->admin->toString($newObject)),
                            'points' => $event->getPoints(),
                            'reason' => $event->getReason() ?: $reason,
                        ],
                        'lotgd_core_admin'
                    )
                );

                $dispatcher->dispatch($event, DonationManualEvent::POST);

                //-- Redirect to list
                return $this->redirectTo($newObject);
            }
            catch (ModelManagerException $e)
            {
                $this->handleModelManagerException($e);
            }
        }

        $formView = $form->createView();
        //-- Set the theme for the current Admin Form
        $this->get('twig')->getRuntime(FormRenderer::class)->setTheme($formView, $this->admin->getFormTheme());

        return $this->renderWithExtraParams('@LotgdCore/donation/list__action_add_manual.html.twig', [
            'action'   => 'add_manual',
            'form'     => $formView,
            'object'   => $newObject,
            'objectId' => null,
        ]);
    }
}
