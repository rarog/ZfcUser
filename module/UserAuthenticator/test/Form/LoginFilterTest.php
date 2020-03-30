<?php

namespace UserAuthenticatorTest\Form;

use Laminas\Validator\EmailAddress;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\LoginFilter as Filter;
use UserAuthenticator\Options\ModuleOptions;

class LoginFilterTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Form\LoginFilter::__construct
     */
    public function testConstruct(): void
    {
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $options->expects($this->once())
                ->method('getAuthIdentityFields')
                ->will($this->returnValue([]));

        $filter = new Filter($options);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('credential', $inputs);

        $this->assertEquals(0, $inputs['identity']->getValidatorChain()->count());
    }

    /**
     * @covers UserAuthenticator\Form\LoginFilter::__construct
     */
    public function testConstructIdentityEmail(): void
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

        $identity = $inputs['identity'];

        // test email as identity
        $validators = $identity->getValidatorChain()->getValidators();
        $this->assertArrayHasKey('instance', $validators[0]);
        $this->assertInstanceOf(EmailAddress::class, $validators[0]['instance']);
    }
}
