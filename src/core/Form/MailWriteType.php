<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\Form;

use Laminas\Filter\Digits;
use Laminas\Filter\StripTags;
use Laminas\Filter;
use Lotgd\Core\Controller\MailController;
use Lotgd\Core\Form\Type\AutocompleteType;
use Lotgd\Core\Form\Type\TextareaLimitType;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Pattern\StimulusUrlTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MailWriteType extends AbstractType
{
    use StimulusUrlTrait;

    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('return_to', HiddenType::class, [
                'filters' => [
                    new Digits(),
                ],
            ])
            ->add('to', AutocompleteType::class, [
                'label'     => 'section.write.form.to',
                'stimulus_controller' => 'autocomplete-mail',
                'url_value' => $this->getStimulusUrl(MailController::class, 'searchAvatar'),
                'help' => 'section.write.mail.notice',
                'help_attr' => [
                    'data-autocomplete-mail-target' => 'superuserMessage',
                    'class' => 'hidden rounded mt-1 p-2 bg-red-50 text-red-900 text-sm'
                ]
            ])
            ->add('subject', TextType::class, [
                'label'       => 'section.write.form.subject',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 255]),
                ],
                'filters' => [
                    new StripTags(),
                ],
            ])
            ->add('body', TextareaLimitType::class, [
                'label'            => 'section.write.form.body',
                'characters_limit' => $this->settings->getSetting('mailsizelimit', 1024),
                'constraints'      => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 0, 'max' => 65000]),
                ],
                'filters' => [
                    new StripTags(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'translation_domain' => 'jaxon_mail',
        ]);
    }
}
