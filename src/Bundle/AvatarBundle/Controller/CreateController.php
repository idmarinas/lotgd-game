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

use Lotgd\Bundle\AvatarBundle\Entity\Avatar;
use Lotgd\Bundle\CoreBundle\Tool\Lotgd;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/avatar")
 *
 * @IsGranted("ROLE_USER")
 */
final class CreateController extends AbstractController
{
    public const TRANSLATOR_DOMAIN = 'lotgd_avatar_page_create';

    /**
     * @Route("/create", name="lotgd_avatar_create")
     */
    public function create(Request $request, TranslatorInterface $translator, Lotgd $lotgdFunctions)
    {
        //-- Check count of avatars and not allow create more if have max
        if (count($this->getUser()->getAvatars()) >= $this->getParameter('lotgd_avatar.avatar.max_per_user'))
        {
            $this->addFlash('warning', $translator->trans('flash.max_avatars_per_user', [], self::TRANSLATOR_DOMAIN));

            return $this->redirectToRoute('lotgd_user_profile');
        }

        $entity = new Avatar();
        $entity->setUser($this->getUser());
        $form = $this->createForm($this->getParameter('lotgd_avatar.form.create_avatar'), $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $title     = $lotgdFunctions->getCharacterTitle();
            $titleName = $title->{$entity->getSex() ? 'getFemale' : 'getMale'}();

            $entity->setTitle($titleName);

            $this->getDoctrine()->getManager()->persist($entity);
            $this->getDoctrine()->getManager()->flush();

            //-- Update user with avatar
            $this->getUser()->setAvatar($entity);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $translator->trans('form.success.create_avatar', [], self::TRANSLATOR_DOMAIN));

            return $this->redirectToRoute('lotgd_user_profile');
        }

        return $this->render('@LotgdAvatar/avatar/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
