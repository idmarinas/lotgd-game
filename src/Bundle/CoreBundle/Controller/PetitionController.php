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
use Lotgd\Bundle\CoreBundle\Repository\PetitionsRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/petition")
 */
class PetitionController extends AbstractController
{
    public const TRANSLATOR_DOMAIN = 'lotgd_core_page_petition';

    private $validator;
    private $translator;
    private $mailer;

    public function __construct(ValidatorInterface $validator, TranslatorInterface $translator, MailerInterface $mailer)
    {
        $this->validator  = $validator;
        $this->translator = $translator;
        $this->mailer     = $mailer;
    }

    /**
     * @Route("/help", name="lotgd_core_petition_help")
     */
    public function help(Request $request, PetitionsRepository $repository): Response
    {
        $networkRestriction = false;
        $messageSend        = '';
        $messageNetwork     = '';
        $count              = $repository->getCountPetitionsForNetwork($request->getClientIp());

        //-- Not allow to much petitions form same network, but yes from admins
        if ($count >= 5 && ! $this->isGranted('ROLE_ADMIN'))
        {
            $networkRestriction = true;
            $messageNetwork     = 'flash.message.section.default.error.network';
        }

        $form = $this->createForm(PetitionType::class, null, [
            'petitions' => \explode(',', $this->getParameter('lotgd_bundle.petitions.types')),
            'action'    => $this->generateUrl('lotgd_core_petition_help'),
        ]);
        $formClone = clone $form;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && ! $networkRestriction)
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

            $messageSend = 'flash.message.section.default.success.send';
            $form        = $formClone;

            // Notify to admin if if wants it
            $this->emailPetitionToAdmin($entity);
        }

        return $this->render('@LotgdCore/petition/help.html.twig', [
            'form'            => $form->createView(),
            'message_send'    => $messageSend,
            'network_message' => $messageNetwork,
            'network_count'   => $count,
        ]);
    }

    /**
     * If the admin wants it, email the petitions to them.
     */
    private function emailPetitionToAdmin(Petitions $entity): void
    {
        $adminEmail = $this->getParameter('lotgd_bundle.game.server.admin.email');
        $validEmail = $this->validator->validate($adminEmail, [
            new Assert\NotBlank(),
            new Assert\NotNull(),
            new Assert\Email(),
        ]);

        if ( ! $this->getParameter('lotgd_bundle.game.server.admin.mailed_petitions') || 'postmaster@localhost.com' == $adminEmail || \count($validEmail))
        {
            return;
        }

        $host   = $this->request->server->get('HTTP_HOST');
        $schema = 'On' == $this->request->server->get('HTTPS') ? 'https' : 'http';
        $url    = "{$schema}://{$host}/";

        $email = (new TemplatedEmail())
            ->from(new Address('mailer@lotgd.core', 'LoTGD Mail Bot'))
            ->to($adminEmail)
            ->subject($this->translator->trans('mailer.email.subject', [], self::TRANSLATOR_DOMAIN))
            ->htmlTemplate('@LotgdCore/petition/email.html.twig')
            ->context([
                'translator_domain' => self::TRANSLATOR_DOMAIN,
                'url'               => $url,
                'entity'            => $entity,
            ])
        ;

        $this->mailer->send($email);
    }
}
