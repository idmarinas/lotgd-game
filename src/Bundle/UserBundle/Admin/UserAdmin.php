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

namespace Lotgd\Bundle\UserBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type as SonataType;
use Sonata\AdminBundle\Form\Type\CollectionType;

final class UserAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id', null, ['label' => 'entity.user.id'])
            ->add('isVerified', null, ['label' => 'entity.user.is_verified'])
            ->add('bannedUntil', null, ['label' => 'entity.user.banned_until'])
            ->add('username', null, ['label' => 'entity.user.username'])
            ->add('email', null, ['label' => 'entity.user.email'])
            ->add('createdAt', null, ['label' => 'entity.user.created_at'])
            ->add('roles', null, ['label' => 'entity.user.roles'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', null, ['label' => 'entity.user.id'])
            ->add('isBanned', 'boolean', ['mapped' => false, 'label' => 'entity.user.is_banned', 'inverse' => true])
            ->add('isDeleted', 'boolean', ['label' => 'entity.user.is_deleted', 'inverse' => true])
            ->add('username', null, ['label' => 'entity.user.username'])
            ->add('email', null, ['label' => 'entity.user.email'])
            ->add('isVerified', null, ['editable' => true, 'label' => 'entity.user.is_verified'])
            ->add('createdAt', null, ['label' => 'entity.user.created_at'])
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
            ->tab('form.group_info')
                ->with('form.group_basic', ['class' => 'col-md-8', 'label' => null, 'box_class' => null])
                    ->add('id', null, ['label' => 'entity.user.id', 'disabled' => true])
                    ->add('username', null, ['label' => 'entity.user.username', 'disabled' => true])
                    ->add('email', null, ['label' => 'entity.user.email', 'disabled' => true])
                    ->add('createdAt', SonataType\DateTimePickerType::class, ['disabled' => true, 'label' => 'entity.user.created_at', 'dp_side_by_side' => true])
                ->end()
                ->with('form.group_roles', ['class' => 'col-md-4', 'label' => null, 'box_class' => null])
                    ->add('isVerified', null, ['label' => 'entity.user.is_verified'])
                    ->add('roles', CollectionType::class, [
                        'label' => 'entity.user.roles',
                        'allow_add' => true,
                        'allow_delete' => true
                    ])
                    ->add('bannedUntil', SonataType\DateTimePickerType::class, ['required' => false, 'label' => 'entity.user.banned_until'])
                    ->add('lastMotd', SonataType\DateTimePickerType::class, ['required' => false, 'label' => 'entity.user.last_motd'])
                ->end()
            ->end()
            ->tab('form.group_avatar')
                ->with('form.group_avatar', ['class' => 'col-md-6', 'label' => null, 'box_class' => null])
                    ->add('avatar', null, ['label' => 'entity.user.avatar'])
                ->end()
                ->with('form.group_avatars', ['class' => 'col-md-6', 'label' => null, 'box_class' => null])
                    ->add('avatars', null, ['label' => 'entity.user.avatars'])
                ->end()
            ->end()
            ->tab('form.group_other')
                ->with('form.group_donation', ['class' => 'col-md-6'])
                    ->add('donation', null, ['label' => 'entity.user.donation'])
                    ->add('donationSpent', null, ['label' => 'entity.user.donation_spent'])
                ->end()
                ->with('form.group_referer', ['class' => 'col-md-6'])
                    ->add('referer', null, ['label' => 'entity.user.referer', 'choice_label' => 'username'])
                    ->add('refererIsRewarded', null, ['label' => 'entity.user.referer_is_rewarded'])
                ->end()
                ->with('form.group_other')
                    ->add('deletedAt', SonataType\DateTimePickerType::class, ['label' => 'entity.user.deleted_at', 'required' => false])
                ->end()
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id', null, ['label' => 'entity.user.id'])
            ->add('isVerified', null, ['label' => 'entity.user.is_verified'])
            ->add('isDeleted', 'boolean', ['label' => 'entity.user.is_deleted', 'inverse' => true])
            ->add('username', null, ['label' => 'entity.user.username'])
            ->add('email', null, ['label' => 'entity.user.email'])
            ->add('lastMotd', null, ['label' => 'entity.user.last_motd'])
            ->add('isBanned', 'boolean', ['mapped' => false, 'label' => 'entity.user.is_banned', 'inverse' => true])
            ->add('bannedUntil', null, ['label' => 'entity.user.banned_until'])
            ->add('createdAt', null, ['label' => 'entity.user.created_at'])
            ->add('updatedAt', null, ['label' => 'entity.user.updated_at'])
            ->add('referer', null, ['label' => 'entity.user.referer', 'associated_property' => 'username'])
            ->add('refererIsRewarded', null, ['label' => 'entity.user.referer_is_rewarded'])
            ->add('donation', null, ['label' => 'entity.user.donation'])
            ->add('donationSpent', null, ['label' => 'entity.user.donation_spent'])
            ->add('deletedAt', null, ['label' => 'entity.user.deleted_at'])
            ->add('roles', 'array', ['label' => 'entity.user.roles'])
        ;
    }
}
