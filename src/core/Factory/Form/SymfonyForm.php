<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\Factory\Form;

use Bukashk0zzz\FilterBundle\Form\Extension\FormTypeExtension;
use Bukashk0zzz\FilterBundle\Service\Filter;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Lotgd\Core\Doctrine\Persistance\ManagerRegistry;
use Lotgd\Core\Form\Extension\FiltersExtension;
use Lotgd\Core\Symfony\Validator\ConstraintValidatorFactory;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Symfony\Component\Validator\Validation;

class SymfonyForm implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $em                = $container->get(\Lotgd\Core\Db\Doctrine::class);
        $managerRegistry   = new ManagerRegistry('default', [], ['default' => $em], null, 'default', 'Doctrine\\ORM\\Proxy\\Proxy');
        $csrfGenerator     = new UriSafeTokenGenerator();
        $csrfStorage       = new NativeSessionTokenStorage();
        $csrfManager       = new CsrfTokenManager($csrfGenerator, $csrfStorage);
        $filter            = new Filter(new CachedReader(new AnnotationReader(), $em->getConfiguration()->getMetadataCacheImpl()));
        $constraintFactory = new ConstraintValidatorFactory();
        $constraintFactory->addValidator('doctrine.orm.validator.unique', new UniqueEntityValidator($managerRegistry));

        $validator = Validation::createValidatorBuilder();
        $validator->setConstraintValidatorFactory($constraintFactory);
        $validator->enableAnnotationMapping();

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new CsrfExtension($csrfManager))
            ->addExtension(new ValidatorExtension($validator->getValidator()))
            ->addTypeExtension(new FormTypeExtension($filter, true))
            ->addTypeExtension(new FiltersExtension())
        ;

        return $formFactory->getFormFactory();
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
