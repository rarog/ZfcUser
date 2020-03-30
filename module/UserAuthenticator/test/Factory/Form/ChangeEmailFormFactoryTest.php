<?php

namespace UserAuthenticatorTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Factory\Form\ChangeEmailFactory;
use UserAuthenticator\Form\ChangeEmail;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Mapper\User as UserMapper;

class ChangeEmailFormFactoryTest extends TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager([
            'services' => [
                'zfcuser_module_options' => new ModuleOptions(),
                'zfcuser_user_mapper' => new UserMapper()
            ]
        ]);

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new ChangeEmailFactory();

        $this->assertInstanceOf(ChangeEmail::class, $factory->__invoke($serviceManager, ChangeEmail::class));
    }
}
