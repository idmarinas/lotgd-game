<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Bundle\CoreBundle\Entity\Referers;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Register HTTP REFERER.
 *
 * @TODO Add setting to configure if register or not.
 */
class RefererEventSubscriber implements EventSubscriberInterface
{
    private $request;
    private $doctrine;

    public function __construct(RequestStack $request, EntityManagerInterface $doctrine)
    {
        $this->request  = $request->getCurrentRequest();
        $this->doctrine = $doctrine;
    }

    public function onKernelRequest()
    {
        $url  = $this->request->server->get('SERVER_NAME');
        $uri  = $this->request->server->get('HTTP_REFERER');
        $site = $uri ? \parse_url($uri, PHP_URL_HOST) : '';

        //-- Ignore if not have $uri or $site or referer is the server
        if ($url == $site || \parse_url($url, PHP_URL_HOST) == $site || ! $uri || ! $site)
        {
            return;
        }

        $url = \sprintf('%s://%s/%s',
            $this->request->server->get('REQUEST_SCHEME', $this->request->server->get('SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME')),
            $this->request->server->get('SERVER_NAME'),
            $this->request->server->get('REQUEST_URI')
        );

        $refererRepository = $this->doctrine->getRepository(Referers::class);
        $entity            = $refererRepository->findOneBy(['uri' => $uri]);
        $entity            = $entity ?: new Referers();

        $entity->setUri($uri)
            ->incrementCount()
            ->setLast(new \DateTime('now'))
            ->setSite($site)
            ->setDest($url)
            ->setIp($this->request->server->get('REMOTE_ADDR'))
        ;

        $this->doctrine->persist($entity);
        $this->doctrine->flush();
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
