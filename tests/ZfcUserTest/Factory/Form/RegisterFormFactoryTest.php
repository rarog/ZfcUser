<?php

namespace ZfcUserTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\ServiceManager;
use ZfcUser\Factory\Form\Register as RegisterFactory;
use ZfcUser\Form\Register;
use ZfcUser\Options\ModuleOptions;
use ZfcUser\Mapper\User as UserMapper;
use PHPUnit\Framework\TestCase;

class RegisterFormFactoryTest extends TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('zfcuser_module_options', new ModuleOptions());
        $serviceManager->setService('zfcuser_user_mapper', new UserMapper());
        $serviceManager->setService('zfcuser_register_form_hydrator', new ClassMethodsHydrator());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new RegisterFactory();

        $this->assertInstanceOf(Register::class, $factory->__invoke($serviceManager, Register::class));
    }
}
