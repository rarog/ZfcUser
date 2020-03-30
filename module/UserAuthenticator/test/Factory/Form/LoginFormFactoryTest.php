<?php

namespace UserAuthenticatorTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use UserAuthenticator\Factory\Form\LoginFactory;
use UserAuthenticator\Options\ModuleOptions;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\Login;

class LoginFormFactoryTest extends TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(ModuleOptions::class, new ModuleOptions());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new LoginFactory();

        $this->assertInstanceOf(Login::class, $factory->__invoke($serviceManager, Login::class));
    }
}
