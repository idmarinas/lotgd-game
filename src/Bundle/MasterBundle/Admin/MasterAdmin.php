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

namespace Lotgd\Bundle\MasterBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class MasterAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('creatureid', null, ['label' => 'entity.master.creatureid'])
            ->add('creaturename', null, ['label' => 'entity.master.creaturename'])
            ->add('creaturelevel', null, ['label' => 'entity.master.creaturelevel'])
            ->add('creatureweapon', null, ['label' => 'entity.master.creatureweapon'])
            ->add('creaturelose', null, ['label' => 'entity.master.creaturelose'])
            ->add('creaturewin', null, ['label' => 'entity.master.creaturewin'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('creatureid', null, ['label' => 'entity.master.creatureid'])
            ->add('creaturename', null, ['label' => 'entity.master.creaturename'])
            ->add('creaturelevel', null, ['label' => 'entity.master.creaturelevel'])
            ->add('creatureweapon', null, ['label' => 'entity.master.creatureweapon'])
            // ->add('creaturelose', null, ['label' => 'entity.master.creaturelose'])
            // ->add('creaturewin', null, ['label' => 'entity.master.creaturewin'])
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
            ->add('creaturename', null, ['label' => 'entity.master.creaturename'])
            ->add('creaturelevel', null, ['label' => 'entity.master.creaturelevel'])
            ->add('creatureweapon', null, ['label' => 'entity.master.creatureweapon'])
            ->add('creaturelose', null, ['label' => 'entity.master.creaturelose'])
            ->add('creaturewin', null, ['label' => 'entity.master.creaturewin'])
            ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('creatureid', null, ['label' => 'entity.master.creatureid'])
            ->add('creaturename', null, ['label' => 'entity.master.creaturename'])
            ->add('creaturelevel', null, ['label' => 'entity.master.creaturelevel'])
            ->add('creatureweapon', null, ['label' => 'entity.master.creatureweapon'])
            ->add('creaturelose', null, ['label' => 'entity.master.creaturelose'])
            ->add('creaturewin', null, ['label' => 'entity.master.creaturewin'])
            ;
    }
}
