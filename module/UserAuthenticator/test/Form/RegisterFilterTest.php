<?php

namespace UserAuthenticatorTest\Form;

use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\RegisterFilter as Filter;
use UserAuthenticator\Validator\NoRecordExists;
use UserAuthenticator\Options\ModuleOptions;

class RegisterFilterTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Form\RegisterFilter::__construct
     */
    public function testConstruct(): void
    {
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue(true));
        $options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue(true));

        $emailValidator = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();
        $usernameValidator = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();

        $filter = new Filter($emailValidator, $usernameValidator, $options);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('username', $inputs);
        $this->assertArrayHasKey('email', $inputs);
        $this->assertArrayHasKey('display_name', $inputs);
        $this->assertArrayHasKey('password', $inputs);
        $this->assertArrayHasKey('passwordVerify', $inputs);
    }

    public function testSetGetEmailValidator(): void
    {
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $validatorInit = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();
        $validatorNew = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();

        $filter = new Filter($validatorInit, $validatorInit, $options);

        $this->assertSame($validatorInit, $filter->getEmailValidator());
        $filter->setEmailValidator($validatorNew);
        $this->assertSame($validatorNew, $filter->getEmailValidator());
    }

    public function testSetGetUsernameValidator(): void
    {
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $validatorInit = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();
        $validatorNew = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();

        $filter = new Filter($validatorInit, $validatorInit, $options);

        $this->assertSame($validatorInit, $filter->getUsernameValidator());
        $filter->setUsernameValidator($validatorNew);
        $this->assertSame($validatorNew, $filter->getUsernameValidator());
    }

    public function testSetGetOptions(): void
    {
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $optionsNew = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $validatorInit = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();
        $filter = new Filter($validatorInit, $validatorInit, $options);

        $this->assertSame($options, $filter->getOptions());
        $filter->setOptions($optionsNew);
        $this->assertSame($optionsNew, $filter->getOptions());
    }
}
