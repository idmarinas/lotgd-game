<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\EntityForm;

use Lotgd\Core\Entity\User;
use Lotgd\Core\Form\Type\BitFieldType;
use Lotgd\Core\Form\Type\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        global $session;

        $builder
            ->add('regdate', DateTimeType::class, [
                'label'    => 'acct.regdate',
                'required' => false,
                'disabled' => true,
            ])
            ->add('laston', DateTimeType::class, [
                'label'    => 'acct.laston',
                'required' => false,
                'disabled' => true,
            ])
            ->add('lastmotd', DateTimeType::class, [
                'label'    => 'acct.lastmotd',
                'required' => false,
                'disabled' => true,
            ])
            // ->add('lasthit', DateTimeType::class, [
            //     'label'    => 'acct.lasthit',
            //     'required' => false,
            //     'disabled' => true,
            // ])
            ->add('login', TextType::class, ['label' => 'acct.login'])
            ->add('emailaddress', EmailType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => 'acct.emailaddress',
            ])
            ->add('locked', CheckboxType::class, ['label' => 'acct.locked', 'required' => false])
            ->add('lastip', TextType::class, [
                'label'    => 'acct.lastip',
                'required' => false,
                'disabled' => true,
            ])
            ->add('uniqueid', TextType::class, [
                'label'    => 'acct.uniqueid',
                'required' => false,
                'disabled' => true,
            ])
            ->add('boughtroomtoday', CheckboxType::class, ['label' => 'acct.boughtroomtoday', 'required' => false])
            ->add('banoverride', CheckboxType::class, ['label' => 'acct.banoverride', 'required' => false])
            ->add('referer', NumberType::class, [
                'label'    => 'acct.referer',
                'required' => false,
                'disabled' => ! ($session['user']['superuser'] & SU_EDIT_DONATIONS),
            ])
            ->add('refererawarded', NumberType::class, [
                'label'    => 'acct.refererawarded',
                'required' => false,
                'disabled' => true,
            ])

            ->add('superuser', BitFieldType::class, [
                'label'    => 'acct.superuser.label',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'help'     => 'acct.superuser.help',
                'choices'  => [
                    'acct.superuser.option.su_megauser'  => SU_MEGAUSER,
                    'acct.superuser.option.editor.label' => [
                        'acct.superuser.option.editor.su_edit_config'    => SU_EDIT_CONFIG,
                        'acct.superuser.option.editor.su_edit_users'     => SU_EDIT_USERS,
                        'acct.superuser.option.editor.su_is_banmaster'   => SU_IS_BANMASTER,
                        'acct.superuser.option.editor.su_edit_mounts'    => SU_EDIT_MOUNTS,
                        'acct.superuser.option.editor.su_edit_creatures' => SU_EDIT_CREATURES,
                        'acct.superuser.option.editor.su_edit_equipment' => SU_EDIT_EQUIPMENT,
                        'acct.superuser.option.editor.su_edit_riddles'   => SU_EDIT_RIDDLES,
                        'acct.superuser.option.editor.su_manage_modules' => SU_MANAGE_MODULES,
                    ],
                    'acct.superuser.option.customer.label' => [
                        'acct.superuser.option.customer.su_is_gamemaster'        => SU_IS_GAMEMASTER,
                        'acct.superuser.option.customer.su_edit_petitions'       => SU_EDIT_PETITIONS,
                        'acct.superuser.option.customer.su_edit_comments'        => SU_EDIT_COMMENTS,
                        'acct.superuser.option.customer.su_moderate_clans'       => SU_MODERATE_CLANS,
                        'acct.superuser.option.customer.su_audit_moderation'     => SU_AUDIT_MODERATION,
                        'acct.superuser.option.customer.su_override_yom_warning' => SU_OVERRIDE_YOM_WARNING,
                        'acct.superuser.option.customer.su_post_motd'            => SU_POST_MOTD,
                    ],
                    'acct.superuser.option.donation.label' => [
                        'acct.superuser.option.donation.su_edit_donations' => SU_EDIT_DONATIONS,
                        'acct.superuser.option.donation.su_edit_paylog'    => SU_EDIT_PAYLOG,
                    ],
                    'acct.superuser.option.dev.label' => [
                        'acct.superuser.option.dev.su_infinite_days'  => SU_INFINITE_DAYS,
                        'acct.superuser.option.dev.su_developer'      => SU_DEVELOPER,
                        'acct.superuser.option.dev.su_is_translator'  => SU_IS_TRANSLATOR,
                        'acct.superuser.option.dev.su_debug_output'   => SU_DEBUG_OUTPUT,
                        'acct.superuser.option.dev.su_show_phpnotice' => SU_SHOW_PHPNOTICE,
                        'acct.superuser.option.dev.su_raw_sql'        => SU_RAW_SQL,
                        'acct.superuser.option.dev.su_view_source'    => SU_VIEW_SOURCE,
                        'acct.superuser.option.dev.su_give_grotto'    => SU_GIVE_GROTTO,
                        'acct.superuser.option.dev.su_never_expire'   => SU_NEVER_EXPIRE,
                    ],
                ],
            ])

            ->add('save', SubmitType::class, ['label' => 'save.button'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => User::class,
            'translation_domain' => 'form_core_account',
        ]);
    }
}
