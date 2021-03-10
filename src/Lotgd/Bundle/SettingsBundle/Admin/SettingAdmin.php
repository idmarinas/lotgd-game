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

use Lotgd\Bundle\SettingsBundle\Doctrine\FieldEnum\SettingType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type as SonataType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Lotgd\Bundle\CoreBundle\Form\Type\CheckboxType;
use Lotgd\Bundle\CoreBundle\Form\Type\NumberFloatType;
use Lotgd\Bundle\CoreBundle\Form\Type\NumberType;

final class SettingAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id', null, ['label' => 'entity.setting.id'])
            ->add('name', null, ['label' => 'entity.setting.name'])
            ->add('domain', null, ['label' => 'entity.setting.domain'])
            ->add('type', null, ['label' => 'entity.setting.type'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', null, ['label' => 'entity.setting.id'])
            ->add('domain', null, ['label' => 'entity.setting.domain'])
            ->add('name',null, ['label' => 'entity.setting.name'])
            ->add('description', null, ['label' => 'entity.setting.description'])
            ->add('type', null, ['label' => 'entity.setting.type'])
            ->add('value', 'setting_type_value', ['editable' => true, 'label' => 'entity.setting.value'])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('group.setting', ['label' => null])
                ->add('id', null, ['label' => 'entity.setting.id', 'disabled' => true])
                ->add('domain', SonataType\ModelListType::class, ['label' => 'entity.setting.domain', 'empty_data' => 0])
                ->add('name', null, ['label' => 'entity.setting.name'])
                ->add('description', null, ['label' => 'entity.setting.description'])
                ->add('type', ChoiceType::class, [
                    'label' => 'entity.setting.type',
                    'choices' => SettingType::toArray()
                ])
        ;

        $subject = $this->getSubject();

        //-- Only add value when edit setting.
        if ($this->isCurrentRoute('edit'))
        {
            switch ($subject->getType())
            {
                case 'bool':
                    $formMapper->add('value', CheckboxType::class, ['required' => false, 'label' => 'entity.setting.value']);
                break;
                case 'int':
                    $formMapper->add('value', NumberType::class, ['label' => 'entity.setting.value']);
                case 'float':
                    $formMapper->add('value', NumberFloatType::class, ['label' => 'entity.setting.value']);
                break;
                default: //-- Default is string
                    $formMapper->add('value', null, ['label' => 'entity.setting.value']);
                break;
            }
        }

        $formMapper
                ->add('owner', SonataType\ModelListType::class, ['label' => 'entity.setting.owner'])
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id', null, ['label' => 'entity.setting.id'])
            ->add('domain', null, ['label' => 'entity.setting.domain'])
            ->add('name', null, ['label' => 'entity.setting.name'])
            ->add('description', null, ['label' => 'entity.setting.description'])
            ->add('type', null, ['label' => 'entity.setting.type'])
            ->add('value', null, ['label' => 'entity.setting.value'])
            ->add('owner', null, ['label' => 'entity.setting.owner', 'choice_label' => 'username'])
        ;
    }
}
