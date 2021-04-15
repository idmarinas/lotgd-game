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

final class TitleAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('titleid', null, ['label' => 'entity.title.titleid'])
            ->add('dk', null, ['label' => 'entity.title.dk'])
            // ->add('ref', null, ['label' => 'entity.title.ref'])
            ->add('male', null, ['label' => 'entity.title.male'])
            ->add('female', null, ['label' => 'entity.title.female'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('titleid', null, ['label' => 'entity.title.titleid'])
            ->add('dk', null, ['label' => 'entity.title.dk'])
            // ->add('ref', null, ['label' => 'entity.title.ref'])
            ->add('male', null, ['label' => 'entity.title.male'])
            ->add('female', null, ['label' => 'entity.title.female'])
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
            ->add('dk', null, ['label' => 'entity.title.dk'])
            // ->add('ref', null, ['label' => 'entity.title.ref'])
            ->add('male', null, ['label' => 'entity.title.male'])
            ->add('female', null, ['label' => 'entity.title.female'])
            ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('titleid', null, ['label' => 'entity.title.titleid'])
            ->add('dk', null, ['label' => 'entity.title.dk'])
            // ->add('ref', null, ['label' => 'entity.title.ref'])
            ->add('male', null, ['label' => 'entity.title.male'])
            ->add('female', null, ['label' => 'entity.title.female'])
            ;
    }
}
