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

namespace Lotgd\Bundle\MountBundle\Admin;

use Lotgd\Bundle\CoreBundle\Form\Type\BuffType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class MountAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('mountid', null, ['label' => 'entity.mount.mountid'])
            ->add('mountactive', null, ['label' => 'entity.mount.mountactive'])
            ->add('mountname', null, ['label' => 'entity.mount.mountname'])
            ->add('mountdesc', null, ['label' => 'entity.mount.mountdesc'])
            ->add('mountcategory', null, ['label' => 'entity.mount.mountcategory'])
            // ->add('mountbuff', null, ['label' => 'entity.mount.mountbuff'])
            ->add('mountcostgems', null, ['label' => 'entity.mount.mountcostgems'])
            ->add('mountcostgold', null, ['label' => 'entity.mount.mountcostgold'])
            ->add('mountforestfights', null, ['label' => 'entity.mount.mountforestfights'])
            ->add('newday', null, ['label' => 'entity.mount.newday'])
            ->add('recharge', null, ['label' => 'entity.mount.recharge'])
            ->add('partrecharge', null, ['label' => 'entity.mount.partrecharge'])
            ->add('mountfeedcost', null, ['label' => 'entity.mount.mountfeedcost'])
            ->add('mountlocation', null, ['label' => 'entity.mount.mountlocation'])
            ->add('mountdkcost', null, ['label' => 'entity.mount.mountdkcost'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('mountid', null, ['label' => 'entity.mount.mountid'])
            ->add('mountactive', null, ['editable' => true, 'label' => 'entity.mount.mountactive'])
            ->add('mountname', null, ['label' => 'entity.mount.mountname'])
            // ->add('mountdesc', null, ['label' => 'entity.mount.mountdesc'])
            ->add('mountcategory', null, ['label' => 'entity.mount.mountcategory'])
            // ->add('mountbuff', null, ['label' => 'entity.mount.mountbuff'])
            // ->add('mountcostgems', null, ['label' => 'entity.mount.mountcostgems'])
            // ->add('mountcostgold', null, ['label' => 'entity.mount.mountcostgold'])
            ->add('mountforestfights', null, ['label' => 'entity.mount.mountforestfights'])
            // ->add('newday', null, ['label' => 'entity.mount.newday'])
            // ->add('recharge', null, ['label' => 'entity.mount.recharge'])
            // ->add('partrecharge', null, ['label' => 'entity.mount.partrecharge'])
            // ->add('mountfeedcost', null, ['label' => 'entity.mount.mountfeedcost'])
            ->add('mountlocation', null, ['label' => 'entity.mount.mountlocation'])
            // ->add('mountdkcost', null, ['label' => 'entity.mount.mountdkcost'])
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
                    ->add('mountactive', null, ['label' => 'entity.mount.mountactive'])
                    ->add('mountlocation', null, ['label' => 'entity.mount.mountlocation'])
                    ->add('mountname', null, ['label' => 'entity.mount.mountname'])
                    ->add('mountcategory', null, ['label' => 'entity.mount.mountcategory'])
                    ->add('mountdesc', null, ['label' => 'entity.mount.mountdesc'])
                ->end()
            ->end()
            ->tab('form.tab_bonus')
                ->with('form.group_bonus', ['label' => null, 'box_class' => null])
                    ->add('mountforestfights', null, ['label' => 'entity.mount.mountforestfights'])
                ->end()
            ->end()
            ->tab('form.tab_buff')
                ->with('form.group_buff', ['label' => null, 'box_class' => null])
                    ->add('mountbuff', BuffType::class, ['label' => 'entity.mount.mountbuff'])
                ->end()
            ->end()
            ->tab('form.tab_cost')
                ->with('form.group_cost', ['label' => null, 'box_class' => null])
                    ->add('mountdkcost', null, ['label' => 'entity.mount.mountdkcost'])
                    ->add('mountcostgems', null, ['label' => 'entity.mount.mountcostgems'])
                    ->add('mountcostgold', null, ['label' => 'entity.mount.mountcostgold'])
                    ->add('mountfeedcost', null, ['label' => 'entity.mount.mountfeedcost'])
                ->end()
            ->end()
            ->tab('form.tab_message')
                ->with('form.group_message', ['label' => null, 'box_class' => null])
                    ->add('newday', null, ['label' => 'entity.mount.newday'])
                    ->add('recharge', null, ['label' => 'entity.mount.recharge'])
                    ->add('partrecharge', null, ['label' => 'entity.mount.partrecharge'])
                ->end()
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('mountid', null, ['label' => 'entity.mount.mountid'])
            ->add('mountname', null, ['label' => 'entity.mount.mountname'])
            ->add('mountdesc', null, ['label' => 'entity.mount.mountdesc'])
            ->add('mountcategory', null, ['label' => 'entity.mount.mountcategory'])
            ->add('mountbuff', null, ['label' => 'entity.mount.mountbuff'])
            ->add('mountcostgems', null, ['label' => 'entity.mount.mountcostgems'])
            ->add('mountcostgold', null, ['label' => 'entity.mount.mountcostgold'])
            ->add('mountactive', null, ['label' => 'entity.mount.mountactive'])
            ->add('mountforestfights', null, ['label' => 'entity.mount.mountforestfights'])
            ->add('newday', null, ['label' => 'entity.mount.newday'])
            ->add('recharge', null, ['label' => 'entity.mount.recharge'])
            ->add('partrecharge', null, ['label' => 'entity.mount.partrecharge'])
            ->add('mountfeedcost', null, ['label' => 'entity.mount.mountfeedcost'])
            ->add('mountlocation', null, ['label' => 'entity.mount.mountlocation'])
            ->add('mountdkcost', null, ['label' => 'entity.mount.mountdkcost'])
            ;
    }
}
