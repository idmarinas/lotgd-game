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

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class LogdnetAdmin extends AbstractAdmin
{
    //-- Configure routes
    protected function configureRoutes(RouteCollection $collection)
    {
        //-- Not allow create and edit
        $collection->remove('create');
        $collection->remove('edit');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('serverid', null, ['label' => 'entity.logdnet.serverid'])
            ->add('address', null, ['label' => 'entity.logdnet.address'])
            ->add('description', null, ['label' => 'entity.logdnet.description'])
            ->add('priority', null, ['label' => 'entity.logdnet.priority'])
            ->add('lastupdate', null, ['label' => 'entity.logdnet.lastupdate'])
            ->add('version', null, ['label' => 'entity.logdnet.version'])
            ->add('admin', null, ['label' => 'entity.logdnet.admin'])
            ->add('lastping', null, ['label' => 'entity.logdnet.lastping'])
            ->add('recentips', null, ['label' => 'entity.logdnet.recentips'])
            ->add('count', null, ['label' => 'entity.logdnet.count'])
            ->add('lang', null, ['label' => 'entity.logdnet.lang'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('serverid', null, ['label' => 'entity.logdnet.serverid'])
            ->add('address', null, ['label' => 'entity.logdnet.address'])
            ->add('count', null, ['label' => 'entity.logdnet.count'])
            // ->add('description', null, ['label' => 'entity.logdnet.description'])
            // ->add('priority', null, ['label' => 'entity.logdnet.priority'])
            ->add('lastupdate', null, ['label' => 'entity.logdnet.lastupdate'])
            ->add('version', null, ['label' => 'entity.logdnet.version'])
            // ->add('admin', null, ['label' => 'entity.logdnet.admin'])
            // ->add('lastping', null, ['label' => 'entity.logdnet.lastping'])
            ->add('recentips', null, ['label' => 'entity.logdnet.recentips'])
            // ->add('lang', null, ['label' => 'entity.logdnet.lang'])
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
            ->add('serverid', null, ['label' => 'entity.logdnet.serverid'])
            ->add('address', null, ['label' => 'entity.logdnet.address'])
            ->add('description', null, ['label' => 'entity.logdnet.description'])
            ->add('priority', null, ['label' => 'entity.logdnet.priority'])
            ->add('lastupdate', null, ['label' => 'entity.logdnet.lastupdate'])
            ->add('version', null, ['label' => 'entity.logdnet.version'])
            ->add('admin', null, ['label' => 'entity.logdnet.admin'])
            ->add('lastping', null, ['label' => 'entity.logdnet.lastping'])
            ->add('recentips', null, ['label' => 'entity.logdnet.recentips'])
            ->add('count', null, ['label' => 'entity.logdnet.count'])
            ->add('lang', null, ['label' => 'entity.logdnet.lang'])
            ;
    }
}
