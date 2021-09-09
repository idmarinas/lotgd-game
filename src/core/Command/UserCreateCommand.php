<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.0.0
 */

namespace Lotgd\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Repository\UserRepository;
use Lotgd\Core\Lib\Settings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command to create a new user.
 * Note: only allow create 1 admin from this command.
 */
final class UserCreateCommand extends Command
{
    public const TEXT_DOMAIN = 'console_command';

    protected const SU_MEGAUSER = SU_MEGAUSER | SU_EDIT_MOUNTS | SU_EDIT_CREATURES | SU_EDIT_PETITIONS | SU_EDIT_COMMENTS | SU_EDIT_DONATIONS | SU_EDIT_USERS | SU_EDIT_CONFIG | SU_INFINITE_DAYS | SU_EDIT_EQUIPMENT | SU_EDIT_PAYLOG | SU_DEVELOPER | SU_POST_MOTD | SU_MODERATE_CLANS | SU_EDIT_RIDDLES | SU_MANAGE_MODULES | SU_AUDIT_MODERATION | SU_RAW_SQL | SU_VIEW_SOURCE | SU_NEVER_EXPIRE;

    protected static $defaultName = 'lotgd:user:create';

    protected $doctrine;
    protected $translator;
    protected $validator;
    protected $settings;
    protected $accountRepository;
    protected $passwordEncoder;

    public function __construct(
        EntityManagerInterface $doctrine,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        Settings $settings,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        parent::__construct();

        $this->doctrine   = $doctrine;
        $this->translator = $translator;
        $this->validator  = $validator;
        $this->settings   = $settings;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Create a new user in LoTGD')
            ->setHelp(
                <<<'EOT'
                    The <info>%command.name%</info> command allow create a new user in LoTGD.
                    EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $login    = $this->getLoginName($input, $output);
        $email    = $this->getEmail($input, $output);
        $password = $this->getPassword($input, $output);
        $isAdmin  = $this->getIsAdmin($input, $output);

        try
        {
            //-- Configure account
            $account = new \Lotgd\Core\Entity\User();
            $account->setLogin($login)
                ->setEmailaddress($email)
                ->setRegdate(new \DateTime())
            ;

            $password = $this->passwordEncoder->encodePassword($account, $password);
            $account->setPassword($password);

            //-- Need for get a ID of new account
            $this->doctrine->persist($account);
            $this->doctrine->flush(); //Persist objects

            //-- Configure character
            $title     = $this->getCharacterTitle();
            $character = new \Lotgd\Core\Entity\Avatar();
            $character->setPlayername($login)
                ->setName("{$title} {$login}")
                ->setTitle($title)
                ->setAcct($account)
                ->setGold((int) $this->settings->getSetting('newplayerstartgold', 50))
                ->setLocation((string) $this->settings->getSetting('villagename', LOCATION_FIELDS))
            ;

            if ($isAdmin)
            {
                $account->setSuperuser(self::SU_MEGAUSER);

                $character
                    ->setName("`%Admin`0 `&{$login}`0")
                    ->setCtitle('`%Admin`0')
                ;
            }

            //-- Need for get ID of new character
            $this->doctrine->persist($character);
            $this->doctrine->flush(); //-- Persist objects

            //-- Set ID of character and update Account
            $account->setAvatar($character);
            $this->doctrine->persist($account);
            $this->doctrine->flush(); //-- Persist objects
        }
        catch (\Throwable $th)
        {
            $style->text($th->getMessage());

            $style->error($this->translator->trans('user.create.fail', [], self::TEXT_DOMAIN));

            return 1;
        }

        $style->success($this->translator->trans('user.create.success', [], self::TEXT_DOMAIN));

        return 0;
    }

    private function getLoginName(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');

        $question = new Question($this->translator->trans('user.create.question.login', [], self::TEXT_DOMAIN));
        $question->setValidator(function ($value)
        {
            $errors = $this->validator->validate((string) $value, [
                new Assert\Length([ 'min' => 3, 'max' => 25]),
                new Assert\Callback(function ($login, ExecutionContextInterface $context)
                {
                    $exists = null !== $this->getAccountRepository()->findOneByLogin($login);

                    if ($exists)
                    {
                        $context->addViolation($this->translator->trans('user.create.question.login.exists', [], self::TEXT_DOMAIN));
                    }
                }),
            ]);

            foreach ($errors as $error)
            {
                throw new \DomainException($error->getMessage());
            }

            return $value;
        });

        return $helper->ask($input, $output, $question);
    }

    private function getEmail(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');

        $question = new Question($this->translator->trans('user.create.question.email', [], self::TEXT_DOMAIN));
        $question->setValidator(function ($value)
        {
            $errors = $this->validator->validate((string) $value, [
                new Assert\NotBlank(),
                new Assert\NotNull(),
                new Assert\Email(),
                new Assert\Callback(function ($email, ExecutionContextInterface $context)
                {
                    $exists = null !== $this->getAccountRepository()->findOneByEmailaddress($email);

                    if ($exists && $email)
                    {
                        $context->addViolation($this->translator->trans('user.create.question.email.exists', [], self::TEXT_DOMAIN));
                    }
                }),
            ]);

            foreach ($errors as $error)
            {
                throw new \DomainException($error->getMessage());
            }

            return $value;
        });

        return (string) $helper->ask($input, $output, $question);
    }

    private function getPassword(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');

        $question = new Question($this->translator->trans('user.create.question.password.one', [], self::TEXT_DOMAIN));
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $question->setValidator(function ($value)
        {
            $errors = $this->validator->validate((string) $value, [
                new Assert\NotBlank(),
                new Assert\NotNull(),
                new Assert\Length([ 'min' => 3 ]),
                new Assert\NotCompromisedPassword(),
            ]);

            foreach ($errors as $error)
            {
                throw new \DomainException($error->getMessage());
            }

            return $value;
        });

        $questionR = new Question($this->translator->trans('user.create.question.password.two', [], self::TEXT_DOMAIN));
        $questionR->setHidden(true);
        $questionR->setHiddenFallback(false);

        do
        {
            $password       = $helper->ask($input, $output, $question);
            $confirmPasword = $helper->ask($input, $output, $questionR);

            if ($password != $confirmPasword)
            {
                $text = $this->translator->trans('user.create.question.password.not.match', [], self::TEXT_DOMAIN);
                $output->writeln("<error>{$text}</>");
            }
        } while ($password != $confirmPasword);

        return (string) $password;
    }

    private function getIsAdmin(InputInterface $input, OutputInterface $output): bool
    {
        /** @var Lotgd\Core\Repository\UserRepository $superusers */
        $superusers = (bool) $this->getAccountRepository()->getSuperuserCountWithPermit(SU_MEGAUSER);

        if ($superusers)
        { //-- If exist one super user not allow create more
            return false;
        }

        $helper   = $this->getHelper('question');
        $choices  = ['No (Normal account)', 'Yes (Only one admin can be create with this command)'];
        $question = new ChoiceQuestion(
            $this->translator->trans('user.create.question.is.admin', [], self::TEXT_DOMAIN),
            $choices,
            0
        );

        return (bool) \array_search($helper->ask($input, $output, $question), $choices);
    }

    private function getCharacterTitle()
    {
        $query  = $this->doctrine->createQueryBuilder();
        $result = $query
            ->select('u')
            ->from('LotgdCore:Titles', 'u')
            ->where('u.dk <= :dk')
            ->orderBy('rand()')

            ->setParameter('dk', 0)

            ->setMaxResults(1)

            ->getQuery()
            ->getSingleResult()
        ;

        if ($result)
        {
            return $result->getMale();
        }

        return '';
    }

    private function getAccountRepository(): UserRepository
    {
        if ( ! $this->accountRepository instanceof UserRepository)
        {
            $this->accountRepository = $this->doctrine->getRepository('LotgdCore:User');
        }

        return $this->accountRepository;
    }
}
