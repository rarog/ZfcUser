<?php

namespace UserAuthenticator\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Form\Register;
use UserAuthenticator\Form\RegisterFilter;
use UserAuthenticator\Mapper\User;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Validator\NoRecordExists;

class RegisterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $options = $serviceManager->get(ModuleOptions::class);
        $form = new Register(null, $options);

        $form->setHydrator($serviceManager->get(ClassMethodsHydrator::class));
        $form->setInputFilter(new RegisterFilter(
            new NoRecordExists([
                'mapper' => $serviceManager->get(User::class),
                'key'    => 'email'
            ]),
            new NoRecordExists([
                'mapper' => $serviceManager->get(User::class),
                'key'    => 'username'
            ]),
            $options
        ));

        return $form;
    }
}
