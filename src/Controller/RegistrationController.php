<?php

namespace Lotgd\Core\Controller;

use Lotgd\Core\Entity\User;
use Lotgd\Core\Form\RegistrationFormType;
use Lotgd\Core\Security\EmailVerifier;
use Lotgd\Core\Security\LoginFormAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Translation\TranslatableMessage;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public const TEXT_DOMAIN = 'page_registration';

    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="lotgd_core_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        //-- Redirect to profile if user is authenticated
        if ($this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('lotgd_core_profile');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('lotgd_core_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('mailer@lotgd.core', 'LoTGD Mail Bot'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('page/user/registration/confirmation_email.html.twig')
                    ->context(['text_domain' => self::TEXT_DOMAIN])
            );
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('page/user/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="lotgd_core_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try
        {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        }
        catch (VerifyEmailExceptionInterface $exception)
        {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('lotgd_core_register');
        }

        $this->addFlash('success', new TranslatableMessage('email.verified', [], self::TEXT_DOMAIN));

        return $this->redirectToRoute('lotgd_core_profile');
    }
}
