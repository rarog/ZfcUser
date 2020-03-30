<?php

namespace UserAuthenticatorTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\ServiceManager;
use UserAuthenticator\Factory\Form\RegisterFactory;
use UserAuthenticator\Form\Register;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Mapper\User as UserMapper;
use PHPUnit\Framework\TestCase;

class RegisterFormFactoryTest extends TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(ModuleOptions::class, new ModuleOptions());
        $serviceManager->setService(UserMapper::class, new UserMapper());
        $serviceManager->setService(ClassMethodsHydrator::class, new ClassMethodsHydrator());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new RegisterFactory();

        $this->assertInstanceOf(Register::class, $factory->__invoke($serviceManager, Register::class));
    }
}
