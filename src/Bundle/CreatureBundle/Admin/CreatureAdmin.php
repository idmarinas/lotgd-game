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

namespace Lotgd\Bundle\CreatureBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Lotgd\Bundle\CoreBundle\Form\Type\NumberFloatType;

final class CreatureAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('creatureid', null, ['label' => 'entity.creature.creatureid'])
            ->add('creaturename', 'doctrine_orm_translation_field', ['label' => 'entity.creature.creaturename'])
            ->add('creaturecategory', 'doctrine_orm_translation_field', ['label' => 'entity.creature.creaturecategory'])
            // ->add('creatureimage', null, ['label' => 'entity.creature.creatureimage'])
            // ->add('creaturedescription')
            ->add('creatureweapon', 'doctrine_orm_translation_field', ['label' => 'entity.creature.creatureweapon'])
            // ->add('creaturegoldbonus')
            // ->add('creatureattackbonus')
            // ->add('creaturedefensebonus')
            // ->add('creaturehealthbonus')
            // ->add('creaturelose')
            // ->add('creaturewin')
            // ->add('creatureaiscript')
            ->add('createdby', null, ['label' => 'entity.creature.createdby'])
            ->add('forest', null, ['label' => 'entity.creature.forest'])
            ->add('graveyard', null, ['label' => 'entity.creature.graveyard'])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('creatureid', null, ['label' => 'entity.creature.creatureid'])
            ->add('creaturename', null, ['label' => 'entity.creature.creaturename'])
            ->add('creaturecategory', null, ['label' => 'entity.creature.creaturecategory'])
            // ->add('creatureimage', null, ['label' => 'entity.creature.creatureimage'])
            // ->add('creaturedescription', null, ['label' => 'entity.creature.creaturedescription'])
            ->add('creatureweapon', null, ['label' => 'entity.creature.creatureweapon'])
            // ->add('creaturegoldbonus', null, ['label' => 'entity.creature.creaturegoldbonus'])
            // ->add('creatureattackbonus', null, ['label' => 'entity.creature.creatureattackbonus'])
            // ->add('creaturedefensebonus', null, ['label' => 'entity.creature.creaturedefensebonus'])
            // ->add('creaturehealthbonus', null, ['label' => 'entity.creature.creaturehealthbonus'])
            // ->add('creaturelose', null, ['label' => 'entity.creature.creaturelose'])
            // ->add('creaturewin', null, ['label' => 'entity.creature.creaturewin'])
            // ->add('creatureaiscript', null, ['label' => 'entity.creature.creatureaiscript'])
            // ->add('createdby', null, ['label' => 'entity.creature.createdby'])
            ->add('forest', null, ['editable' => true, 'label' => 'entity.creature.forest'])
            ->add('graveyard', null, ['editable' => true, 'label' => 'entity.creature.graveyard'])
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
            ->tab('form.tab_properties')
                ->with('form.group_properties', ['label' => null, 'box_class' => null])
                    ->add('creaturename', null, ['label' => 'entity.creature.creaturename'])
                    ->add('creaturecategory', null, ['label' => 'entity.creature.creaturecategory'])
                    ->add('creatureimage', null, [
                        'required' => false,
                        'label' => 'entity.creature.creatureimage'
                    ])
                    ->add('creaturedescription', null, [
                        'required' => false,
                        'label' => 'entity.creature.creaturedescription',
                        'translation_domain' => 'lotgd_creature_admin'
                    ])
                    ->add('creatureweapon', null, ['label' => 'entity.creature.creatureweapon'])
                    ->add('creaturelose', null, ['label' => 'entity.creature.creaturelose'])
                    ->add('creaturewin', null, ['label' => 'entity.creature.creaturewin'])
                ->end()
            ->end()
            ->tab('form.tab_bonus')
                ->with('form.group_bonus', ['label' => null, 'box_class' => null])
                    ->add('creaturegoldbonus', NumberFloatType::class, [
                        'empty_data' => '1',
                        'label' => 'entity.creature.creaturegoldbonus',
                        'help' => 'entity.creature.creaturegoldbonus_help',
                        'translation_domain' => 'lotgd_creature_admin',
                        'attr' => [
                            'min' => 0,
                            'max' => 99.99,
                            'step' => 0.01
                        ]
                    ])
                    ->add('creatureattackbonus', NumberFloatType::class, [
                        'empty_data' => '1',
                        'label' => 'entity.creature.creatureattackbonus',
                        'help' => 'entity.creature.creatureattackbonus_help',
                        'translation_domain' => 'lotgd_creature_admin',
                        'attr' => [
                            'min' => 0,
                            'max' => 99.99,
                            'step' => 0.01
                        ]
                    ])
                    ->add('creaturedefensebonus', NumberFloatType::class, [
                        'empty_data' => '1',
                        'label' => 'entity.creature.creaturedefensebonus',
                        'help' => 'entity.creature.creaturedefensebonus_help',
                        'translation_domain' => 'lotgd_creature_admin',
                        'attr' => [
                            'min' => 0,
                            'max' => 99.99,
                            'step' => 0.01
                        ]
                    ])
                    ->add('creaturehealthbonus', NumberFloatType::class, [
                        'empty_data' => '1',
                        'label' => 'entity.creature.creaturehealthbonus',
                        'help' => 'entity.creature.creaturehealthbonus_help',
                        'translation_domain' => 'lotgd_creature_admin',
                        'attr' => [
                            'min' => 0,
                            'max' => 99.99,
                            'step' => 0.01
                        ]
                    ])
                ->end()
            ->end()
            ->tab('form.tab_other')
                ->with('form.group_other', ['label' => null, 'box_class' => null])
                    ->add('creatureaiscript', null, ['label' => 'entity.creature.creatureaiscript'])
                    ->add('createdby', null, ['label' => 'entity.creature.createdby'])
                    ->add('forest', null, ['label' => 'entity.creature.forest'])
                    ->add('graveyard', null, ['label' => 'entity.creature.graveyard'])
                ->end()
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('creatureid', null, ['label' => 'entity.creature.creatureid'])
            ->add('creaturename', null, ['label' => 'entity.creature.creaturename'])
            ->add('creaturecategory', null, ['label' => 'entity.creature.creaturecategory'])
            ->add('creatureimage', null, ['label' => 'entity.creature.creatureimage'])
            ->add('creaturedescription', null, ['label' => 'entity.creature.creaturedescription'])
            ->add('creatureweapon', null, ['label' => 'entity.creature.creatureweapon'])
            ->add('creaturegoldbonus', null, ['label' => 'entity.creature.creaturegoldbonus'])
            ->add('creatureattackbonus', null, ['label' => 'entity.creature.creatureattackbonus'])
            ->add('creaturedefensebonus', null, ['label' => 'entity.creature.creaturedefensebonus'])
            ->add('creaturehealthbonus', null, ['label' => 'entity.creature.creaturehealthbonus'])
            ->add('creaturelose', null, ['label' => 'entity.creature.creaturelose'])
            ->add('creaturewin', null, ['label' => 'entity.creature.creaturewin'])
            ->add('creatureaiscript', null, ['label' => 'entity.creature.creatureaiscript'])
            ->add('createdby', null, ['label' => 'entity.creature.createdby'])
            ->add('forest', null, ['label' => 'entity.creature.forest'])
            ->add('graveyard', null, ['label' => 'entity.creature.graveyard'])
            ;
    }
}
