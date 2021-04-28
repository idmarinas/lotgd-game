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
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class ReferersAdmin extends AbstractAdmin
{
    //-- Configure routes
    protected function configureRoutes(RouteCollection $collection)
    {
        //-- Not allow ...
        $collection->remove('create');
        $collection->remove('edit');
        $collection->remove('delete');
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query      = parent::configureQuery($query);
        $countQuery = $this->getModelManager()->createQuery($this->getClass(), 'u');
        $prefix     = $query->getRootAliases()[0];

        $countQuery->select('SUM(u.count)')
            ->where("u.site = {$prefix}.site")
        ;

        $query
            ->addSelect('('.$countQuery->getDQL().') AS total_count')
            ->addGroupBy("{$prefix}.site")
        ;

        return $query;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('refererid', null, ['label' => 'entity.referers.refererid'])
            ->add('uri', null, ['label' => 'entity.referers.uri'])
            ->add('count', null, ['label' => 'entity.referers.count'])
            ->add('last', null, ['label' => 'entity.referers.last'])
            ->add('site', null, ['label' => 'entity.referers.site'])
            ->add('dest', null, ['label' => 'entity.referers.dest'])
            ->add('ip', null, ['label' => 'entity.referers.ip'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('refererid', null, ['label' => 'entity.referers.refererid'])
            ->add('site', null, ['label' => 'entity.referers.site'])
            ->add('total_count', FieldDescriptionInterface::TYPE_INTEGER, ['label' => 'entity.referers.total_count'])
            ->add('last', null, ['label' => 'entity.referers.last'])
            ->add('dest', null, ['label' => 'entity.referers.dest'])
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
            ->add('refererid', null, ['label' => 'entity.referers.refererid'])
            ->add('site', null, ['label' => 'entity.referers.site'])
            ->add('count', null, ['label' => 'entity.referers.count'])
            ->add('uri', null, ['label' => 'entity.referers.uri'])
            ->add('dest', null, ['label' => 'entity.referers.dest'])
            ->add('last', null, ['label' => 'entity.referers.last'])
            ->add('ip', null, ['label' => 'entity.referers.ip'])
            ;
    }
}
