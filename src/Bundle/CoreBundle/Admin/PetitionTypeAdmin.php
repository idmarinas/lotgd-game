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

final class PetitionTypeAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'entity.petition_type.id'])
            ->add('name', null, ['label' => 'entity.petition_type.name'])
            ->add('slug', null, ['label' => 'entity.petition_type.slug'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', null, ['label' => 'entity.petition_type.id'])
            ->add('name', null, ['label' => 'entity.petition_type.name'])
            ->add('slug', null, ['label' => 'entity.petition_type.slug'])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', null, ['label' => 'entity.petition_type.name'])
            ->add('slug', null, [
                'attr'  => ['readonly' => true],
                'label' => 'entity.petition_type.slug',
                'help'  => 'entity.petition_type.slug_help',
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'entity.petition_type.id'])
            ->add('name', null, ['label' => 'entity.petition_type.name'])
            ->add('slug', null, ['label' => 'entity.petition_type.slug'])
            ;
    }
}
