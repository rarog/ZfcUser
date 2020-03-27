<?php

namespace ZfcUserTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use ZfcUser\Factory\Form\ChangePassword as ChangePasswordFactory;
use ZfcUser\Form\ChangePassword;
use ZfcUser\Options\ModuleOptions;
use ZfcUser\Mapper\User as UserMapper;

class ChangePasswordFormFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('zfcuser_module_options', new ModuleOptions());
        $serviceManager->setService('zfcuser_user_mapper', new UserMapper());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new ChangePasswordFactory();

        $this->assertInstanceOf(ChangePassword::class, $factory->__invoke($serviceManager, ChangePassword::class));
    }
}
