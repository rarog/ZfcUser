<?php

namespace UserAuthenticator\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Form\RegisterFilter;
use UserAuthenticator\Form\RegisterForm;
use UserAuthenticator\Mapper\User;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Validator\NoRecordExists;

class RegisterFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $moduleOptions = $container->get(ModuleOptions::class);
        $form = new RegisterForm(
            null,
            [
                'module_options' => $moduleOptions,
            ]
        );

        $form->setHydrator($container->get(ClassMethodsHydrator::class));
        $form->setInputFilter(new RegisterFilter(
            new NoRecordExists([
                'mapper' => $container->get(User::class),
                'key'    => 'email'
            ]),
            new NoRecordExists([
                'mapper' => $container->get(User::class),
                'key'    => 'username'
            ]),
            $moduleOptions
        ));

        return $form;
    }
}
