<?php

/**
 * This file is part of "LoTGD Core Package".
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.md
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\AdminBundle\Block;

use Psr\Cache\CacheItemInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

final class DashboardRssServiceBlock extends AbstractBlockService
{
    private $client;
    private $cache;

    public function __construct(Environment $twig, HttpClientInterface $client, CacheInterface $lotgdCorePackagecache)
    {
        parent::__construct($twig);

        $this->client = $client;
        $this->cache  = $lotgdCorePackagecache;
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'lotgd_admin_default',
            'template'           => '@LotgdAdmin/block/dashboard_rss.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $settings = $blockContext->getSettings();

        $feeds = $this->cache->get('lotgd_admin.dashboard.rss.feed', function (CacheItemInterface $item)
        {
            $item->expiresAfter(600); //-- Cache for 1 hour
            $content = '';

            try
            {
                $request    = $this->client->request('GET', 'https://community.laeradelosdioses.com/forum/14-core-game.xml');
                $statusCode = $request->getStatusCode();
                $content    = $request->getContent();
            }
            catch (\Throwable $th)
            {
                //-- Silence error
            }

            $feeds   = [];
            if ($content && ($statusCode >= 200 && $statusCode < 400))
            {
                try
                {
                    $xml = new \SimpleXMLElement($content);

                    for ($i = 0; $i < 5; ++$i)
                    {
                        $feeds[$i] = $xml->channel->item[$i];
                    }
                }
                catch (\Exception $e)
                {
                    // silently fail error
                }
            }

            empty($feeds) && $item->expiresAfter(0); //-- Expire now if feeds is empty

            return $feeds;
        });

        return $this->renderResponse($blockContext->getTemplate(), [
            'feeds'              => $feeds,
            'block'              => $blockContext->getBlock(),
            'translation_domain' => $settings['translation_domain'],
        ], $response);
    }

    public function getCacheKeys(BlockInterface $block): array
    {
        return [
            'id' => 'admin_dashboard_rss_service',
        ];
    }
}
