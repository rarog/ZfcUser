<?php

namespace UserAuthenticatorTest\Form;

use Laminas\Validator\EmailAddress;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\ChangePasswordFilter as Filter;
use UserAuthenticator\Options\ModuleOptions;

class ChangePasswordFilterTest extends TestCase
{
    public function testConstruct(): void
    {
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(['email']));

        $filter = new Filter($options);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('credential', $inputs);
        $this->assertArrayHasKey('newCredential', $inputs);
        $this->assertArrayHasKey('newCredentialVerify', $inputs);

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
                ->will($this->returnValue($onlyEmail ? ['email'] : ['username']));

        $filter = new Filter($options);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('credential', $inputs);
        $this->assertArrayHasKey('newCredential', $inputs);
        $this->assertArrayHasKey('newCredentialVerify', $inputs);

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

    public function providerTestConstructIdentityEmail(): array
    {
        return [
            [true],
            [false]
        ];
    }
}
