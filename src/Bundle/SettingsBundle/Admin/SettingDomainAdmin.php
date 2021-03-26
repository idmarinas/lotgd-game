<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.md
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Filter\NumberType;
use Sonata\AdminBundle\Show\ShowMapper;

final class SettingDomainAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name', null, ['label' => 'entity.setting_domain.name'])
            ->add('priority', null, ['label' => 'entity.setting_domain.priority'])
            ->add('enabled', null, ['label' => 'entity.setting_domain.enabled'])
            ->add('readOnly', null, ['label' => 'entity.setting_domain.read_only'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', null, ['label' => 'entity.setting_domain.id'])
            ->add('name', null, ['label' => 'entity.setting_domain.name'])
            ->add('priority', null, ['label' => 'entity.setting_domain.priority'])
            ->add('enabled', null, ['label' => 'entity.setting_domain.enabled'])
            ->add('readOnly', null, ['label' => 'entity.setting_domain.read_only'])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('group.setting_domain', ['label' => null])
                ->add('id', null, ['disabled' => true, 'label' => 'entity.setting_domain.id'])
                ->add('name', null, ['label' => 'entity.setting_domain.name'])
                ->add('priority', null, ['label' => 'entity.setting_domain.priority'])
                ->add('enabled', null, ['label' => 'entity.setting_domain.enabled'])
                ->add('readOnly', null, ['label' => 'entity.setting_domain.read_only'])
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id', null, ['label' => 'entity.setting_domain.id'])
            ->add('name', null, ['label' => 'entity.setting_domain.name'])
            ->add('priority', null, ['label' => 'entity.setting_domain.priority'])
            ->add('enabled', null, ['label' => 'entity.setting_domain.enabled'])
            ->add('readOnly', null, ['label' => 'entity.setting_domain.read_only'])
        ;
    }
}
