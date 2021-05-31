<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form\AboutType;

use Lotgd\Core\Form\Type\ViewOnlyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Message size limit per message
            ->add('mailsizelimit', ViewOnlyType::class, [
                'label'        => 'mail.mailsizelimit',
                'apply_filter' => 'numeral',
            ])
            // Limit # of messages in inbox
            ->add('inboxlimit', ViewOnlyType::class, [
                'label'        => 'mail.inboxlimit',
                'apply_filter' => 'numeral',
            ])
            // Automatically delete old messages after (days)
            ->add('oldmail', ViewOnlyType::class, [
                'label'        => 'mail.oldmail',
                'apply_filter' => 'numeral',
            ])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
