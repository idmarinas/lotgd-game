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
use Sonata\AdminBundle\Show\ShowMapper;

final class WeaponAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('weaponid', null, ['label' => 'entity.weapon.weaponid'])
            ->add('weaponname', null, ['label' => 'entity.weapon.weaponname'])
            ->add('value', null, ['label' => 'entity.weapon.value'])
            ->add('damage', null, ['label' => 'entity.weapon.damage'])
            ->add('level', null, ['label' => 'entity.weapon.level'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('weaponid', null, ['label' => 'entity.weapon.weaponid'])
            ->add('weaponname', null, ['label' => 'entity.weapon.weaponname'])
            ->add('value', null, ['label' => 'entity.weapon.value'])
            ->add('damage', null, ['label' => 'entity.weapon.damage'])
            ->add('level', null, ['label' => 'entity.weapon.level'])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('weaponname', null, ['label' => 'entity.weapon.weaponname'])
            ->add('value', null, ['label' => 'entity.weapon.value'])
            ->add('damage', null, ['label' => 'entity.weapon.damage'])
            ->add('level', null, ['label' => 'entity.weapon.level'])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('weaponid', null, ['label' => 'entity.weapon.weaponid'])
            ->add('weaponname', null, ['label' => 'entity.weapon.weaponname'])
            ->add('value', null, ['label' => 'entity.weapon.value'])
            ->add('damage', null, ['label' => 'entity.weapon.damage'])
            ->add('level', null, ['label' => 'entity.weapon.level'])
        ;
    }
}
