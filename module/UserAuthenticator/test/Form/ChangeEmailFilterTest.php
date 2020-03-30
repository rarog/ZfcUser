<?php

namespace UserAuthenticatorTest\Form;

use Laminas\Validator\EmailAddress;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\ChangeEmailFilter as Filter;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Validator\NoRecordExists;

class ChangeEmailFilterTest extends TestCase
{
    public function testConstruct(): void
    {
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(['email']));

        $validator = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();
        $filter = new Filter($options, $validator);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('newIdentity', $inputs);
        $this->assertArrayHasKey('newIdentityVerify', $inputs);

        $validators = $inputs['identity']->getValidatorChain()->getValidators();
        $this->assertArrayHasKey('instance', $validators[0]);
        $this->assertInstanceOf(EmailAddress::class, $validators[0]['instance']);
    }

    /**
     * @dataProvider providerTestConstructIdentityEmail
     */
    public function testConstructIdentityEmail($onlyEmail): void
    {
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(($onlyEmail) ? ['email'] : ['username']));

        $validator = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();
        $filter = new Filter($options, $validator);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('newIdentity', $inputs);
        $this->assertArrayHasKey('newIdentityVerify', $inputs);

        $identity = $inputs['identity'];

        if ($onlyEmail === false) {
            $this->assertEquals(0, $inputs['identity']->getValidatorChain()->count());
        } else {
            // test email as identity
            $validators = $identity->getValidatorChain()->getValidators();
            $this->assertArrayHasKey('instance', $validators[0]);
            $this->assertInstanceOf(EmailAddress::class, $validators[0]['instance']);
        }
    }

    public function testSetGetEmailValidator(): void
    {
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue([]));

        $validatorInit = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();
        $validatorNew = $this->getMockBuilder(NoRecordExists::class)->disableOriginalConstructor()->getMock();

        $filter = new Filter($options, $validatorInit);

        $this->assertSame($validatorInit, $filter->getEmailValidator());
        $filter->setEmailValidator($validatorNew);
        $this->assertSame($validatorNew, $filter->getEmailValidator());
    }

    public function providerTestConstructIdentityEmail(): array
    {
        return [
            [true],
            [false]
        ];
    }
}
