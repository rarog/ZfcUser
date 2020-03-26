<?php

namespace ZfcUserTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use ZfcUser\Factory\Form\Login as LoginFactory;
use ZfcUser\Options\ModuleOptions;
use PHPUnit\Framework\TestCase;
use ZfcUser\Form\Login;

class LoginFormFactoryTest extends TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('zfcuser_module_options', new ModuleOptions());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new LoginFactory();

        $this->assertInstanceOf(Login::class, $factory->__invoke($serviceManager, Login::class));
    }
}
