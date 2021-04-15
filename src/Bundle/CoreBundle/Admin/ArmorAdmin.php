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

final class ArmorAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('armorid', null, ['label' => 'entity.armor.armorid'])
            ->add('armorname', null, ['label' => 'entity.armor.armorname'])
            ->add('value', null, ['label' => 'entity.armor.value'])
            ->add('defense', null, ['label' => 'entity.armor.defense'])
            ->add('level', null, ['label' => 'entity.armor.level'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('armorid', null, ['label' => 'entity.armor.armorid'])
            ->add('armorname', null, ['label' => 'entity.armor.armorname'])
            ->add('value', null, ['label' => 'entity.armor.value'])
            ->add('defense', null, ['label' => 'entity.armor.defense'])
            ->add('level', null, ['label' => 'entity.armor.level'])
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
            ->add('armorname', null, ['label' => 'entity.armor.armorname'])
            ->add('value', null, ['label' => 'entity.armor.value'])
            ->add('defense', null, ['label' => 'entity.armor.defense'])
            ->add('level', null, ['label' => 'entity.armor.level'])
            ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('armorid', null, ['label' => 'entity.armor.armorid'])
            ->add('armorname', null, ['label' => 'entity.armor.armorname'])
            ->add('value', null, ['label' => 'entity.armor.value'])
            ->add('defense', null, ['label' => 'entity.armor.defense'])
            ->add('level', null, ['label' => 'entity.armor.level'])
            ;
    }
}
