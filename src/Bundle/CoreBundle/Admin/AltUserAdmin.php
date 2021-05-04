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

final class AltUserAdmin extends AbstractAdmin
{
    protected $baseRouteName    = 'admin_lotgd_core_alt_user';
    protected $baseRoutePattern = 'lotgd/core/alt_user';
    protected $classnameLabel   = 'alt_user';

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query  = parent::configureQuery($query);
        $dataIp = $this->getModelManager()->createQuery($this->getClass(), 'u');
        $prefix = $query->getRootAliases()[0];

        $dataIp->select('COUNT(1)')
            ->where("u.ipAddress = {$prefix}.ipAddress AND u.id != {$prefix}.id")
        ;

        $query
            ->addSelect('('.$dataIp->getDQL().') AS count_ip')
        ;

        return $query;
    }

    //-- Configure routes
    protected function configureRoutes(RouteCollection $collection)
    {
        //-- Not allow ...
        $collection->remove('create');
        $collection->remove('edit');
        $collection->remove('delete');
        $collection->remove('show');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('username', null, ['label' => 'entity.alt_user.username'])
            ->add('email', null, ['label' => 'entity.alt_user.email'])
            ->add('ipAddress', null, ['label' => 'entity.alt_user.ip_address'])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('username', null, ['label' => 'entity.alt_user.username'])
            ->add('email', null, ['label' => 'entity.alt_user.email'])
            ->add('ip_address', null, ['label' => 'entity.alt_user.ip_address'])
            ->add('count_ip', FieldDescriptionInterface::TYPE_INTEGER, ['label' => 'entity.alt_user.count_ip'])
            ->add('createdAt', null, ['label' => 'entity.alt_user.created_at'])
            ->add('updatedAt', null, ['label' => 'entity.alt_user.updated_at'])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                ],
            ])
        ;
    }
}
