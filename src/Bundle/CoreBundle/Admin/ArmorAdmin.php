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
    private const VALUES = [1 => 48, 225, 585, 990, 1575, 2250, 2790, 3420, 4230, 5040, 5850, 6840, 8010, 9000, 10350];

    public function preUpdate($object)
    {
        $object->setValue(self::VALUES[$object->getDefense()] ?? 48);
    }

    public function prePersist($object)
    {
        $object->setValue(self::VALUES[$object->getDefense()] ?? 48);
    }

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
            ->add('value', null, ['attr' => ['readonly' => true], 'label' => 'entity.armor.value'])
            ->add('defense', null, ['attr' => ['max' => 15, 'min' => 1], 'label' => 'entity.armor.defense'])
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
