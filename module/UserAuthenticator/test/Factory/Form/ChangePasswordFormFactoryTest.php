<?php

namespace UserAuthenticatorTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Factory\Form\ChangePasswordFormFactory;
use UserAuthenticator\Form\ChangePasswordForm;
use UserAuthenticator\Mapper\User as UserMapper;
use UserAuthenticator\Options\ModuleOptions;

class ChangePasswordFormFactoryTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Factory\Form\ChangePasswordFormFactory::__invoke
     */
    public function testFactory(): void
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(ModuleOptions::class, new ModuleOptions());
        $serviceManager->setService(UserMapper::class, new UserMapper());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new ChangePasswordFormFactory();

        $this->assertInstanceOf(
            ChangePasswordForm::class,
            $factory->__invoke($serviceManager, ChangePasswordForm::class)
        );
    }
}
