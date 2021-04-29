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

use Lotgd\Bundle\CoreBundle\Entity\Petition;
use Lotgd\Bundle\CoreBundle\Form\PetitionType;
use Lotgd\Bundle\CoreBundle\Repository\PetitionRepository;
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
    public function help(Request $request, PetitionRepository $repository): Response
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

        $entity = (new Petition())
            ->setIpAddress($request->getClientIp())
        ;

        //-- If user is logged in set user and avatar
        if ($this->getUser())
        {
            $entity->setUser($this->getUser())
                ->setAvatar($this->getUser()->getAvatar())
                ->setAvatarName($this->getUser()->getAvatar()->getName())
                ->setUserOfAvatar($this->getUser()->getUsername())
            ;
        }

        $form = $this->createForm(PetitionType::class, $entity, [
            'action'    => $this->generateUrl('lotgd_core_petition_help'),
        ]);
        $formClone = clone $form;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && ! $networkRestriction)
        {
            $entity     = $form->getData();
            $doctrine = $this->getDoctrine()->getManager();

            $doctrine->persist($entity);
            $doctrine->flush();

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
    private function emailPetitionToAdmin(Petition $entity): void
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
