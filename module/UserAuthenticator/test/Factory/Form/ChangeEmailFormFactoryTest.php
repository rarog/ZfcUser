<?php

namespace UserAuthenticatorTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Factory\Form\ChangeEmailFormFactory;
use UserAuthenticator\Form\ChangeEmailForm;
use UserAuthenticator\Mapper\User as UserMapper;
use UserAuthenticator\Options\ModuleOptions;

class ChangeEmailFormFactoryTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Factory\Form\ChangeEmailFormFactory::__invoke
     */
    public function testFactory()
    {
        $serviceManager = new ServiceManager([
            'services' => [
                ModuleOptions::class => new ModuleOptions(),
                UserMapper::class => new UserMapper()
            ]
        ]);

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new ChangeEmailFormFactory();

        $this->assertInstanceOf(
            ChangeEmailForm::class,
            $factory->__invoke($serviceManager, ChangeEmailForm::class)
        );
    }
}
