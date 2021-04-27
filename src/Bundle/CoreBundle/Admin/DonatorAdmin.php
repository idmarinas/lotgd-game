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

namespace Lotgd\Bundle\CoreBundle\Admin;

use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class DonatorAdmin extends AbstractAdmin
{
    protected $baseRouteName    = 'admin_lotgd_core_donator';
    protected $baseRoutePattern = 'lotgd/core/donator';
    protected $classnameLabel   = 'donator';

    //-- Configure routes
    protected function configureRoutes(RouteCollection $collection)
    {
        //-- Not allow ...
        $collection->remove('create');
        $collection->remove('edit');
        $collection->remove('delete');

        $collection->add('add_manual');
    }

    protected function configureTabMenu(ItemInterface $menu, $action, ?AdminInterface $childAdmin = null)
    {
        if ('list' == $action)
        {
            $menu->addChild('list.label__add_manual', [
                'uri' => $this->generateUrl('add_manual'),
            ]);
        }
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'entity.donator.id'])
            ->add('username', null, ['label' => 'entity.donator.username'])
            ->add('email', null, ['label' => 'entity.donator.email'])
            ->add('donation', null, ['label' => 'entity.donator.donation'])
            ->add('donationSpent', null, ['label' => 'entity.donator.donation_spent'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', null, ['label' => 'entity.donator.id'])
            ->add('username', null, ['label' => 'entity.donator.username'])
            ->add('email', null, ['label' => 'entity.donator.email'])
            ->add('donation', null, ['label' => 'entity.donator.donation'])
            ->add('donationSpent', null, ['label' => 'entity.donator.donation_spent'])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'entity.donator.id'])
            ->add('username', null, ['label' => 'entity.donator.username'])
            ->add('email', null, ['label' => 'entity.donator.email'])
            ->add('donation', null, ['label' => 'entity.donator.donation'])
            ->add('donationSpent', null, ['label' => 'entity.donator.donation_spent'])
            ;
    }

    protected function configureExportFields(): array
    {
        //-- Avoid sensitive data to be exported.
        return \array_filter(parent::configureExportFields(), static function (string $v): bool
        {
            return \in_array($v, ['username', 'email', 'donation', 'donationSpent'], true);
        });
    }
}
