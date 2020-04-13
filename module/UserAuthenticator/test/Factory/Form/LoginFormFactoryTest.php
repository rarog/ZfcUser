<?php

namespace UserAuthenticatorTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use UserAuthenticator\Factory\Form\LoginFormFactory;
use UserAuthenticator\Form\LoginForm;
use UserAuthenticator\Options\ModuleOptions;
use PHPUnit\Framework\TestCase;

class LoginFormFactoryTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Factory\Form\LoginFormFactory::__invoke
     */
    public function testFactory()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(ModuleOptions::class, new ModuleOptions());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new LoginFormFactory();

        $this->assertInstanceOf(
            LoginForm::class,
            $factory->__invoke($serviceManager, LoginForm::class)
        );
    }
}
