<?php

namespace UserAuthenticatorTest\Form;

use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\AbstractModuleOptionsForm;
use UserAuthenticator\Options\ModuleOptions;
use ReflectionClass;
use InvalidArgumentException;

class AbstractModuleOptionsFormTest extends TestCase
{
    /**
     * @param mixed $options
     * @return object implementing anonymous class instance
     */
    private function callConstructor($options): object
    {
        return new class (null, $options) extends AbstractModuleOptionsForm {
        };
    }

    private function setUpExceptionExpectation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No module options were passed to the constructor.');
    }

    /**
     * @covers UserAuthenticator\Form\AbstractModuleOptionsForm::__construct
     */
    public function testConstructor()
    {
        $moduleOptions = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();

        $abstractModuleOptionsForm = $this->callConstructor([
            'module_options' => $moduleOptions,
        ]);

        $reflection = new ReflectionClass(AbstractModuleOptionsForm::class);
        $moduleOptionsProperty = $reflection->getProperty('moduleOptions');
        $moduleOptionsProperty->setAccessible(true);

        $this->assertSame($moduleOptionsProperty->getValue($abstractModuleOptionsForm), $moduleOptions);
    }

    /**
     * @covers UserAuthenticator\Form\AbstractModuleOptionsForm::__construct
     */
    public function testConstructorExceptionThrownNoArray()
    {
        $this->setUpExceptionExpectation();

        $this->callConstructor('');
    }

    /**
     * @covers UserAuthenticator\Form\AbstractModuleOptionsForm::__construct
     */
    public function testConstructorExceptionThrownNoArrayKey()
    {
        $this->setUpExceptionExpectation();

        $this->callConstructor([]);
    }

    /**
     * @covers UserAuthenticator\Form\AbstractModuleOptionsForm::__construct
     */
    public function testConstructorExceptionThrownNoModuleOptionsPassed()
    {
        $this->setUpExceptionExpectation();

        $this->callConstructor([
            'module_options' => new class () {
            },
        ]);
    }
}
