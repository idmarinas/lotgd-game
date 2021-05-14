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

namespace Lotgd\Bundle\CommentaryBundle\Block;

// use FOS\CommentBundle\Acl\AclCommentManager as CommentManager;
// use FOS\CommentBundle\Acl\AclThreadManager as ThreadManager;
use FOS\CommentBundle\Model\CommentManagerInterface;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CommentaryBlock extends AbstractBlockService
{
    private $threadManager;
    private $commentManager;
    private $request;

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'lotgd_commentary_default',
            'template'           => '@LotgdCommentary/block/commentary.html.twig',
            'title'              => null,
            'section'            => 'commentary',
            'talk_line'          => 'talk_line',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $settings = $blockContext->getSettings();

        $id = $settings['section'] ?: 'commentary';
        $thread = $this->threadManager->findThreadById($id);

        if (null === $thread)
        {
            $thread = $this->threadManager->createThread();
            $thread->setId($id);
            $thread->setPermalink($this->request->getUri());

            //-- Add the thread
            $this->threadManager->saveThread($thread);
        }

        $comments = $this->commentManager->findCommentTreeByThread($thread);

        return $this->renderResponse($blockContext->getTemplate(), [
            'settings' => $settings,
            'block'    => $blockContext->getBlock(),
            'thread'   => $thread,
            'comments' => $comments,
        ], $response)->setTtl(30);
    }

    public function setThread(ThreadManagerInterface $thread): self
    {
        $this->threadManager = $thread;

        return $this;
    }

    public function setComment(CommentManagerInterface $comment): self
    {
        $this->commentManager = $comment;

        return $this;
    }

    public function setRequest(RequestStack $request)
    {
        $this->request = $request->getMasterRequest();
    }
}
