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

use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;

final class DashboardPaylogServiceBlock extends AbstractBlockService
{
    private $cache;
    private $pool;

    public function __construct(Environment $twig, CacheInterface $lotgdCorePackageCache, Pool $pool)
    {
        parent::__construct($twig);

        $this->cache = $lotgdCorePackageCache;
        $this->pool  = $pool;
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'lotgd_admin_default',
            'template'           => '@LotgdAdmin/block/dashboard_paylog.html.twig',
            'icon'               => 'fa-dollar',
            'color'              => 'bg-maroon',
            'code'               => 'lotgd_paylog.admin',
            'filters'            => [],
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $settings = $blockContext->getSettings();
        $admin    = $this->pool->getAdminByAdminCode($blockContext->getSetting('code'));

        $donation = $this->cache->get('lotgd_admin.block.service.dashboard.paylog', function (ItemInterface $item) use ($admin)
        {
            $item->expiresAfter(300); //-- Cache 5 mins
            $now = new \DateTime('now');

            $query = $admin->getModelManager()->createQuery($admin->getClass());
            $query->select('SUM(o.amount)')
                ->where('MONTH(o.processdate) = MONTH(:now)')
                ->setParameter('now', $now)
            ;

            return [
                'amount' => $query->getQuery()->getSingleScalarResult(),
                'month'  => $now->format('F'),
            ];
        });

        return $this->renderResponse($blockContext->getTemplate(), [
            'donation' => $donation,
            'settings' => $settings,
            'admin'    => $admin,
            'block'    => $blockContext->getBlock(),
        ], $response);
    }

    public function getCacheKeys(BlockInterface $block): array
    {
        return [
            'id' => 'admin_dashboard_paylog_service',
        ];
    }
}
