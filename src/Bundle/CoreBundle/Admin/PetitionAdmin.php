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

use Lotgd\Bundle\CoreBundle\Doctrine\FieldEnum\PetitionStatusTypeEnum;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class PetitionAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'entity.petition.id'])
            ->add('user', null, ['label' => 'entity.petition.user'])
            ->add('avatar', null, ['label' => 'entity.petition.avatar'])
            ->add('avatarName', null, ['label' => 'entity.petition.avatar_name'])
            ->add('userOfAvatar', null, ['label' => 'entity.petition.user_of_avatar'])
            ->add('email', null, ['label' => 'entity.petition.email'])
            ->add('subject', null, ['label' => 'entity.petition.subject'])
            ->add('description', null, ['label' => 'entity.petition.description'])
            ->add('status', null, ['label' => 'entity.petition.status'])
            ->add('ipAddress', null, ['label' => 'entity.petition.ip_address'])
            ->add('closeDate', null, ['label' => 'entity.petition.close_date'])
            ->add('closeUser', null, ['label' => 'entity.petition.close_user'])
            ->add('createdAt', null, ['label' => 'entity.petition.created_at'])
            ->add('updatedAt', null, ['label' => 'entity.petition.updated_at'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        dump(PetitionStatusTypeEnum::values());
        $list
            ->add('id', null, ['label' => 'entity.petition.id'])
            ->add('userOfAvatar', null, ['label' => 'entity.petition.user_of_avatar'])
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                'label'     => 'entity.petition.status',
                'editable'  => true,
                'choices'   => PetitionStatusTypeEnum::toChoices(),
                'catalogue' => 'lotgd_core_admin',
            ])
            ->add('subject', null, ['label' => 'entity.petition.subject'])
            ->add('closeDate', null, ['label' => 'entity.petition.close_date'])
            ->add('closeUser', null, ['label' => 'entity.petition.close_user'])
            ->add('createdAt', null, ['label' => 'entity.petition.created_at'])
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
            // ->add('avatarName', null, ['label' => 'entity.petition.avatar_name'])
            // ->add('userOfAvatar', null, ['label' => 'entity.petition.user_of_avatar'])
            // ->add('email', null, ['label' => 'entity.petition.email'])
            // ->add('subject', null, ['label' => 'entity.petition.subject'])
            // ->add('description', null, ['label' => 'entity.petition.description'])
            ->add('status', ChoiceType::class, [
                'label'                     => 'entity.petition.status',
                'choices'                   => \array_flip(PetitionStatusTypeEnum::toChoices()),
                'choice_translation_domain' => 'lotgd_core_admin',
            ])
            // ->add('ipAddress', null, ['label' => 'entity.petition.ip_address'])
            // ->add('closeDate', null, ['label' => 'entity.petition.close_date'])
            // ->add('closeUser', null, ['label' => 'entity.petition.close_user'])
            // ->add('createdAt', null, ['label' => 'entity.petition.created_at'])
            // ->add('updatedAt', null, ['label' => 'entity.petition.updated_at'])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'entity.petition.id'])
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                'label'     => 'entity.petition.status',
                'choices'   => PetitionStatusTypeEnum::toChoices(),
                'catalogue' => 'lotgd_core_admin',
            ])
            ->add('user', null, ['label' => 'entity.petition.user'])
            ->add('avatar', null, ['label' => 'entity.petition.avatar'])
            ->add('avatarName', null, ['label' => 'entity.petition.avatar_name'])
            ->add('userOfAvatar', null, ['label' => 'entity.petition.user_of_avatar'])
            ->add('email', null, ['label' => 'entity.petition.email'])
            ->add('ipAddress', null, ['label' => 'entity.petition.ip_address'])
            ->add('closeDate', null, ['label' => 'entity.petition.close_date'])
            ->add('closeUser', null, ['label' => 'entity.petition.close_user'])
            ->add('createdAt', null, ['label' => 'entity.petition.created_at'])
            ->add('updatedAt', null, ['label' => 'entity.petition.updated_at'])
            ->add('subject', null, ['label' => 'entity.petition.subject'])
            ->add('description', null, ['label' => 'entity.petition.description'])
        ;
    }
}
