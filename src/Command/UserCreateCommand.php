<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.0.0
 */

namespace Lotgd\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Repository\UserRepository;
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

    protected static $defaultName = 'lotgd:user:create';

    protected $doctrine;
    protected $translator;
    protected $validator;
    protected $encoder;
    protected $userRepository;

    public function __construct(
        EntityManagerInterface $doctrine,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $encoder
    ) {
        parent::__construct();

        $this->doctrine   = $doctrine;
        $this->translator = $translator;
        $this->validator  = $validator;
        $this->encoder    = $encoder;
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

        try
        {
            //-- Configure user
            $user = new \Lotgd\Core\Entity\User();
            $user->setUsername($this->getUsername($input, $output))
                ->setEmail($this->getEmail($input, $output))
                ->setIsVerified(true)
            ;

            if ($this->getIsAdmin($input, $output))
            {
                $user->setRoles(['ROLE_SUPER_ADMIN']);
            }

            $user->setPassword(
                $this->encoder->encodePassword(
                    $user,
                    $this->getPassword($input, $output)
                )
            );

            //-- Save new user
            $this->doctrine->persist($user);
            $this->doctrine->flush(); //Persist objects
        }
        catch (\Throwable $th)
        {
            $style->info($th->getMessage());

            $style->error($this->translator->trans('user.create.fail', [], self::TEXT_DOMAIN));

            return Command::FAILURE;
        }

        $style->success($this->translator->trans('user.create.success', [], self::TEXT_DOMAIN));

        return Command::SUCCESS;
    }

    private function getUsername(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');

        $question = new Question($this->translator->trans('user.create.question.username', [], self::TEXT_DOMAIN));
        $question->setValidator(function ($value)
        {
            $errors = $this->validator->validate((string) $value, [
                new Assert\Length(null, 3, 25),
                new Assert\Callback(function ($username, ExecutionContextInterface $context)
                {
                    $exists = null !== $this->getuserRepository()->findOneByUsername($username);

                    if ($exists)
                    {
                        $context->addViolation($this->translator->trans('user.create.question.username.exists', [], self::TEXT_DOMAIN));
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
                new Assert\AtLeastOneOf([
                    'constraints' => [
                        new Assert\Blank(),
                        new Assert\IsNull(),
                        new Assert\Email(),
                    ],
                ]),
                new Assert\Callback(function ($email, ExecutionContextInterface $context)
                {
                    $exists = null !== $this->getuserRepository()->findOneByEmail($email);

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
                new Assert\Length([
                    'min' => 6,
                    'max' => 4096, // max length allowed by Symfony for security reasons
                ]),
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
        if ($this->existSuperAdmin())
        { //-- If exist one super user not allow create more
            return false;
        }

        $helper   = $this->getHelper('question');
        $choices  = ['No (Normal user)', 'Yes (Only one admin can be create with this command)'];
        $question = new ChoiceQuestion(
            $this->translator->trans('user.create.question.is.admin', [], self::TEXT_DOMAIN),
            $choices,
            0
        );

        return (bool) \array_search($helper->ask($input, $output, $question), $choices);
    }

    private function existSuperAdmin()
    {
        // Entity manager
        $qb = $this->getUserRepository()->createQueryBuilder('u');

        return (bool) $qb->select('COUNT(1)')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_SUPER_ADMIN"%')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    private function getUserRepository(): UserRepository
    {
        if ( ! $this->userRepository instanceof UserRepository)
        {
            $this->userRepository = $this->doctrine->getRepository('LotgdCore:User');
        }

        return $this->userRepository;
    }
}
