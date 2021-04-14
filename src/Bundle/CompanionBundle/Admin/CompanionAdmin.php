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

namespace Lotgd\Bundle\CompanionBundle\Admin;

use Lotgd\Bundle\CompanionBundle\Form\AbilitiesType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class CompanionAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('companionid', null, ['label' => 'entity.companion.companionid'])
            ->add('companionactive', null, ['label' => 'entity.companion.companionactive'])
            ->add('name', null, ['label' => 'entity.companion.name'])
            ->add('category', null, ['label' => 'entity.companion.category'])
            ->add('description', null, ['label' => 'entity.companion.description'])
            ->add('attack', null, ['label' => 'entity.companion.attack'])
            ->add('attackperlevel', null, ['label' => 'entity.companion.attackperlevel'])
            ->add('defense', null, ['label' => 'entity.companion.defense'])
            ->add('defenseperlevel', null, ['label' => 'entity.companion.defenseperlevel'])
            ->add('maxhitpoints', null, ['label' => 'entity.companion.maxhitpoints'])
            ->add('maxhitpointsperlevel', null, ['label' => 'entity.companion.maxhitpointsperlevel'])
            // ->add('abilities', null, ['label' => 'entity.companion.abilities'])
            ->add('cannotdie', null, ['label' => 'entity.companion.cannotdie'])
            ->add('cannotbehealed', null, ['label' => 'entity.companion.cannotbehealed'])
            ->add('companionlocation', null, ['label' => 'entity.companion.companionlocation'])
            ->add('companioncostdks', null, ['label' => 'entity.companion.companioncostdks'])
            ->add('companioncostgems', null, ['label' => 'entity.companion.companioncostgems'])
            ->add('companioncostgold', null, ['label' => 'entity.companion.companioncostgold'])
            // ->add('jointext',  null, ['label' => 'entity.companion.jointext'])
            // ->add('dyingtext', null, ['label' => 'entity.companion.dyingtext'])
            ->add('allowinshades',null, ['label' => 'entity.companion.allowinshades'])
            ->add('allowinpvp', null, ['label' => 'entity.companion.allowinpvp'])
            ->add('allowintrain', null, ['label' => 'entity.companion.allowintrain'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('companionid', null, ['label' => 'entity.companion.companionid'])
            ->add('companionactive', null, ['editable' => true, 'label' => 'entity.companion.companionactive'])
            ->add('name', null, ['label' => 'entity.companion.name'])
            ->add('category', null, ['label' => 'entity.companion.category'])
            // ->add('description', null, ['label' => 'entity.companion.description'])
            // ->add('attack', null, ['label' => 'entity.companion.attack'])
            // ->add('attackperlevel', null, ['label' => 'entity.companion.attackperlevel'])
            // ->add('defense', null, ['label' => 'entity.companion.defense'])
            // ->add('defenseperlevel', null, ['label' => 'entity.companion.defenseperlevel'])
            // ->add('maxhitpoints', null, ['label' => 'entity.companion.maxhitpoints'])
            // ->add('maxhitpointsperlevel', null, ['label' => 'entity.companion.maxhitpointsperlevel'])
            // ->add('abilities', null, ['label' => 'entity.companion.abilities'])
            // ->add('cannotdie', null, ['label' => 'entity.companion.cannotdie'])
            // ->add('cannotbehealed', null, ['label' => 'entity.companion.cannotbehealed'])
            // ->add('companionlocation', null, ['label' => 'entity.companion.companionlocation'])
            // ->add('companioncostdks', null, ['label' => 'entity.companion.companioncostdks'])
            // ->add('companioncostgems', null, ['label' => 'entity.companion.companioncostgems'])
            // ->add('companioncostgold', null, ['label' => 'entity.companion.companioncostgold'])
            // ->add('jointext',  null, ['label' => 'entity.companion.jointext'])
            // ->add('dyingtext', null, ['label' => 'entity.companion.dyingtext'])
            ->add('allowinshades',null, ['editable' => true, 'label' => 'entity.companion.allowinshades'])
            ->add('allowinpvp', null, ['editable' => true, 'label' => 'entity.companion.allowinpvp'])
            ->add('allowintrain', null, ['editable' => true, 'label' => 'entity.companion.allowintrain'])
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
                    ->add('companionactive', null, ['label' => 'entity.companion.companionactive'])
                    ->add('name', null, ['label' => 'entity.companion.name'])
                    ->add('category', null, ['label' => 'entity.companion.category'])
                    ->add('description', null, ['label' => 'entity.companion.description'])
                    ->add('cannotdie', null, ['label' => 'entity.companion.cannotdie'])
                    ->add('cannotbehealed', null, ['label' => 'entity.companion.cannotbehealed'])
                    ->add('jointext',  null, ['required' => false, 'label' => 'entity.companion.jointext'])
                    ->add('dyingtext', null, ['required' => false, 'label' => 'entity.companion.dyingtext'])
                ->end()
            ->end()
            ->tab('form.tab_attributes')
                ->with('form.group.attributes', ['label' => null, 'box_class' => null])
                    ->add('attack', null, ['label' => 'entity.companion.attack'])
                    ->add('attackperlevel', null, ['label' => 'entity.companion.attackperlevel'])
                    ->add('defense', null, ['label' => 'entity.companion.defense'])
                    ->add('defenseperlevel', null, ['label' => 'entity.companion.defenseperlevel'])
                    ->add('maxhitpoints', null, ['label' => 'entity.companion.maxhitpoints'])
                    ->add('maxhitpointsperlevel', null, ['label' => 'entity.companion.maxhitpointsperlevel'])
                ->end()
            ->end()
            ->tab('form.tab_abilities')
                ->with('form.group_abilities', ['label' => null, 'box_class' => null])
                    ->add('abilities', AbilitiesType::class, ['label' => 'entity.companion.abilities'])
                ->end()
            ->end()
            ->tab('form.tab_cost')
                ->with('form.group_cost', ['label' => null, 'box_class' => null])
                    ->add('companioncostdks', null, ['label' => 'entity.companion.companioncostdks'])
                    ->add('companioncostgems', null, ['label' => 'entity.companion.companioncostgems'])
                    ->add('companioncostgold', null, ['label' => 'entity.companion.companioncostgold'])
                ->end()
            ->end()
            ->tab('form.tab_other')
                ->with('form.group_other', ['label' => null, 'box_class' => null])
                    ->add('companionlocation', null, ['label' => 'entity.companion.companionlocation'])
                    ->add('allowinshades',null, ['label' => 'entity.companion.allowinshades'])
                    ->add('allowinpvp', null, ['label' => 'entity.companion.allowinpvp'])
                    ->add('allowintrain', null, ['label' => 'entity.companion.allowintrain'])
                ->end()
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('companionid', null, ['label' => 'entity.companion.companionid'])
            ->add('name', null, ['label' => 'entity.companion.name'])
            ->add('category', null, ['label' => 'entity.companion.category'])
            ->add('description', null, ['label' => 'entity.companion.description'])
            ->add('attack', null, ['label' => 'entity.companion.attack'])
            ->add('attackperlevel', null, ['label' => 'entity.companion.attackperlevel'])
            ->add('defense', null, ['label' => 'entity.companion.defense'])
            ->add('defenseperlevel', null, ['label' => 'entity.companion.defenseperlevel'])
            ->add('maxhitpoints', null, ['label' => 'entity.companion.maxhitpoints'])
            ->add('maxhitpointsperlevel', null, ['label' => 'entity.companion.maxhitpointsperlevel'])
            ->add('abilities', null, ['label' => 'entity.companion.abilities'])
            ->add('cannotdie', null, ['label' => 'entity.companion.cannotdie'])
            ->add('cannotbehealed', null, ['label' => 'entity.companion.cannotbehealed'])
            ->add('companionlocation', null, ['label' => 'entity.companion.companionlocation'])
            ->add('companionactive', null, ['label' => 'entity.companion.companionactive'])
            ->add('companioncostdks', null, ['label' => 'entity.companion.companioncostdks'])
            ->add('companioncostgems', null, ['label' => 'entity.companion.companioncostgems'])
            ->add('companioncostgold', null, ['label' => 'entity.companion.companioncostgold'])
            ->add('jointext',  null, ['label' => 'entity.companion.jointext'])
            ->add('dyingtext', null, ['label' => 'entity.companion.dyingtext'])
            ->add('allowinshades',null, ['label' => 'entity.companion.allowinshades'])
            ->add('allowinpvp', null, ['label' => 'entity.companion.allowinpvp'])
            ->add('allowintrain', null, ['label' => 'entity.companion.allowintrain'])
            ;
    }
}
