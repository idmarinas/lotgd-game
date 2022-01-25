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

namespace Lotgd\Core\Form\ConfigurationType;

use Laminas\Filter\StripTags;
use LotgdKernel;
use Lotgd\Core\Lib\Settings;
use Symfony\Component\Intl\Languages;
use Laminas\Filter;
use Lotgd\Core\Form\Type\CheckboxType;
use Lotgd\Core\Form\Type\TagifyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class GameSetupType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Allow creation of new characters
        $builder->add('allowcreation', CheckboxType::class, [
            'required' => false,
            'label'    => 'game.setup.allow.creation',
        ]);
        //- Name for the server
        $builder->add('servername', TextType::class, [
            'required'    => true,
            'empty_data'  => 'The Legend of the Green Dragon',
            'label'       => 'game.setup.server.name',
            'constraints' => [
                new Assert\Length(['min' => 3, 'max' => 255, 'allowEmptyString' => false]),
            ],
            'filters' => [
                new StripTags(),
            ],
        ]);
        // Server URL
        $builder->add('serverurl', TextType::class, [
            'required'    => false,
            'label'       => 'game.setup.server.url',
            'constraints' => [
                new Assert\Url(),
            ],
        ]);
        // Login Banner
        $builder->add('loginbanner', TextType::class, [
            'required'    => false,
            'label'       => 'game.setup.login.banner',
            'constraints' => [
                new Assert\Length(['min' => 1, 'max' => 255, 'allowEmptyString' => false]),
            ],
            'filters' => [
                new StripTags(),
            ],
        ]);
        // Max # of players online
        $builder->add('maxonline', NumberType::class, [
            'required'    => false,
            'empty_data'  => 0,
            'label'       => 'game.setup.max.online',
            'constraints' => [
                new Assert\DivisibleBy(1),
            ],
        ]);
        // Admin Email
        $builder->add('gameadminemail', EmailType::class, [
            'required'    => false,
            'label'       => 'game.setup.game.admin.email',
            'empty_data'  => '',
            'constraints' => [
                new Assert\Email(),
            ],
        ]);
        // Should submitted petitions be emailed to Admin Email address?
        $builder->add('emailpetitions', CheckboxType::class, [
            'required' => false,
            'label'    => 'game.setup.email.petitions',
        ]);
        // Languages actives on this server
        $builder->add('serverlanguages', LanguageType::class, [
            'label'    => 'game.setup.server.languages.label',
            'multiple' => true,
            'attr'     => [
                'class' => 'search fluid three column',
            ],
        ]);

        $settings = LotgdKernel::get(Settings::class);
        $server   = \explode(',', $settings->getSetting('serverlanguages'));
        $langs    = Languages::getNames();
        $choices  = \array_filter($langs, function ($key) use ($server)
        {
            return \in_array($key, $server);
        }, ARRAY_FILTER_USE_KEY);

        // Default Language
        $builder->add('defaultlanguage', ChoiceType::class, [
            'choices'    => \array_flip($choices),
            'label'      => 'game.setup.default.language',
            'empty_data' => 'en',
        ]);
        // What types can petitions be?
        $builder->add('petition_types', TagifyType::class, [
            'label'       => 'game.setup.petition.types.label',
            'help'        => 'game.setup.petition.types.note',
            'empty_data'  => 'petition.types.general,petition.types.report.bug,petition.types.suggestion,petition.types.commentpetition.types.other',
            'constraints' => [
                new Assert\Length(['min' => 0, 'max' => 255]),
            ],
        ]);
        // Should DK titles be editable in user editor
        $builder->add('edittitles', CheckboxType::class, [
            'required' => false,
            'label'    => 'game.setup.edit.titles',
        ]);
        // How many items should be shown on the motdlist
        $builder->add('motditems', NumberType::class, [
            'label'       => 'game.setup.motd.items',
            'empty_data'  => 5,
            'constraints' => [
                new Assert\DivisibleBy(1),
            ],
        ]);

        //-- Transformations

        //-- Transform value of server languages
        $builder->get('serverlanguages')
            ->addModelTransformer(new CallbackTransformer(
                function ($value)
                {
                    // transform the string to an array
                    return \explode(',', $value);
                },
                function ($value)
                {
                    // transform the arraty back to a string
                    return \implode(',', $value);
                }
            ))
        ;

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'translation_domain' => 'form_core_configuration',
        ]);
    }
}
