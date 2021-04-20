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
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class PaylogAdmin extends AbstractAdmin
{
    //-- Configure routes
    protected function configureRoutes(RouteCollection $collection)
    {
        //-- Not allow ...
        $collection->remove('create');
        $collection->remove('edit');
        $collection->remove('delete');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('payid', null, ['label' => 'entity.paylog.payid'])
            // ->add('info', null, ['label' => 'entity.paylog.info'])
            // ->add('response', null, ['label' => 'entity.paylog.response'])
            ->add('txnid', null, ['label' => 'entity.paylog.txnid'])
            ->add('amount', null, ['label' => 'entity.paylog.amount'])
            ->add('name', null, ['label' => 'entity.paylog.name'])
            ->add('acctid', null, ['label' => 'entity.paylog.acctid'])
            ->add('processed', null, ['label' => 'entity.paylog.processed'])
            ->add('filed', null, ['label' => 'entity.paylog.filed'])
            ->add('txfee', null, ['label' => 'entity.paylog.txfee'])
            ->add('processdate', null, ['label' => 'entity.paylog.processdate'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('payid', null, ['label' => 'entity.paylog.payid'])
            // ->add('info', null, ['label' => 'entity.paylog.info'])
            // ->add('response', null, ['label' => 'entity.paylog.response'])
            ->add('txnid', null, ['label' => 'entity.paylog.txnid'])
            ->add('amount', null, ['label' => 'entity.paylog.amount'])
            ->add('name', null, ['label' => 'entity.paylog.name'])
            ->add('acctid', null, ['label' => 'entity.paylog.acctid'])
            ->add('processed', null, ['label' => 'entity.paylog.processed'])
            ->add('filed', null, ['label' => 'entity.paylog.filed'])
            ->add('txfee', null, ['label' => 'entity.paylog.txfee'])
            ->add('processdate', null, ['label' => 'entity.paylog.processdate'])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                ],
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('payid', null, ['label' => 'entity.paylog.payid'])
            ->add('info', null, ['label' => 'entity.paylog.info'])
            ->add('response', null, ['label' => 'entity.paylog.response'])
            ->add('txnid', null, ['label' => 'entity.paylog.txnid'])
            ->add('amount', null, ['label' => 'entity.paylog.amount'])
            ->add('name', null, ['label' => 'entity.paylog.name'])
            ->add('acctid', null, ['label' => 'entity.paylog.acctid'])
            ->add('processed', null, ['label' => 'entity.paylog.processed'])
            ->add('filed', null, ['label' => 'entity.paylog.filed'])
            ->add('txfee', null, ['label' => 'entity.paylog.txfee'])
            ->add('processdate', null, ['label' => 'entity.paylog.processdate'])
            ;
    }
}
