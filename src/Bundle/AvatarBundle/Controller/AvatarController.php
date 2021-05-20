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

namespace Lotgd\Bundle\AvatarBundle\Controller;

use Lotgd\Bundle\CoreBundle\Tool\Lotgd;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/avatar")
 *
 * @IsGranted("ROLE_USER")
 */
final class AvatarController extends AbstractController
{
    public const TRANSLATOR_DOMAIN = 'lotgd_avatar_page_avatar';

    /**
     * Change active avatar for user.
     *
     * @Route("/change/{id}", name="lotgd_avatar_change", requirements={"id": "\d+"})
     */
    public function change(int $id, TranslatorInterface $translator): Response
    {
        $flash       = 'error';
        $flashParams = [];
        $doctrine    = $this->getDoctrine()->getManager();

        /** @var \Lotgd\Bundle\AvatarBundle\Entity\Avatar */
        $avatar = $doctrine->getRepository('LotgdAvatar:Avatar')->findOneBy(['id' => $id, 'user' => $this->getUser()]);

        //-- Check if user owner destination avatar
        if ($avatar)
        {
            $flash       = 'success';
            $flashParams = ['name' => $avatar->getPlayername()];

            $this->getUser()->setAvatar($avatar);
            $doctrine->flush();
        }

        $this->addFlash($flash, $translator->trans("flash.controller.change.{$flash}", $flashParams, self::TRANSLATOR_DOMAIN));

        return $this->redirectToRoute('lotgd_user_profile');
    }

    /**
     * Show profile of avatar.
     *
     * @Route("/profile/{id}", name="lotgd_avatar_profile", requirements={"id": "\d+"})
     *
     * @return
     */
    public function profile(int $id): Response
    {
        $doctrine = $this->getDoctrine()->getManager();
        /** @var \Lotgd\Bundle\AvatarBundle\Entity\Avatar */
        $avatar = $doctrine->getRepository('LotgdAvatar:Avatar')->findOneBy(['id' => $id, 'user' => $this->getUser()]);

        if ( ! $avatar)
        {
            return $this->redirectToRoute('lotgd_user_profile');
        }

        return $this->render('@LotgdAvatar/avatar/profile.html.twig', ['avatar' => $avatar]);
    }
}
