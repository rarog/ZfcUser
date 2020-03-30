<?php

namespace UserAuthenticatorTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Factory\Form\ChangePasswordFactory;
use UserAuthenticator\Form\ChangePassword;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Mapper\User as UserMapper;

class ChangePasswordFormFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(ModuleOptions::class, new ModuleOptions());
        $serviceManager->setService(UserMapper::class, new UserMapper());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new ChangePasswordFactory();

        $this->assertInstanceOf(ChangePassword::class, $factory->__invoke($serviceManager, ChangePassword::class));
    }
}
