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

use Lotgd\Bundle\CoreBundle\Entity\Petitions;
use Lotgd\Bundle\CoreBundle\Form\PetitionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/petition")
 */
class PetitionController extends AbstractController
{
    public const TRANSLATOR_DOMAIN = 'lotgd_core_page_petition';

    /**
     * @Route("/help", name="lotgd_core_petition_help")
     */
    public function help(Request $request): Response
    {
        $form = $this->createForm(PetitionType::class, null, [
            'petitions' => \explode(',', $this->getParameter('lotgd_bundle.petitions.types')),
            'action'    => $this->generateUrl('lotgd_core_petition_help'),
        ]);
        $formClone = clone $form;
        $message = '';

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $entity = (new Petitions())
                ->setAuthor($this->getUser() ? $this->getUser()->getId() : 0)
                ->setDate(new \DateTime('now'))
                ->setBody($data)
                ->setPageinfo($request->getSession()->all())
                ->setIp($request->getClientIp())
            ;

            $this->getDoctrine()->getManager()->persist($entity);
            $this->getDoctrine()->getManager()->flush();

            $message = 'flash.message.section.default.success.send';
            $form = $formClone;
        }

        return $this->render('@LotgdCore/petition/help.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }
}
