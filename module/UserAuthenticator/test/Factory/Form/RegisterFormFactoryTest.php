<?php

namespace UserAuthenticatorTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\ServiceManager;
use UserAuthenticator\Factory\Form\RegisterFormFactory;
use UserAuthenticator\Form\RegisterForm;
use UserAuthenticator\Mapper\User as UserMapper;
use UserAuthenticator\Options\ModuleOptions;
use PHPUnit\Framework\TestCase;

class RegisterFormFactoryTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Factory\Form\RegisterFormFactory::__invoke
     */
    public function testFactory()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(ModuleOptions::class, new ModuleOptions());
        $serviceManager->setService(UserMapper::class, new UserMapper());
        $serviceManager->setService(ClassMethodsHydrator::class, new ClassMethodsHydrator());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new RegisterFormFactory();

        $this->assertInstanceOf(
            RegisterForm::class,
            $factory->__invoke($serviceManager, RegisterForm::class)
        );
    }
}
