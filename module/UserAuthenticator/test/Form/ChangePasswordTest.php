<?php

namespace UserAuthenticatorTest\Form;

use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\ChangePassword as Form;
use UserAuthenticator\Options\AuthenticationOptionsInterface;

class ChangePasswordTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Form\ChangePassword::__construct
     */
    public function testConstruct(): void
    {
        $options = $this->getMockBuilder(AuthenticationOptionsInterface::class)
            ->getMock();

        $form = new Form(null, $options);

        $elements = $form->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('credential', $elements);
        $this->assertArrayHasKey('newCredential', $elements);
        $this->assertArrayHasKey('newCredentialVerify', $elements);
        $this->assertArrayHasKey('csrf', $elements);
    }

    /**
     * @covers UserAuthenticator\Form\ChangePassword::getAuthenticationOptions
     * @covers UserAuthenticator\Form\ChangePassword::setAuthenticationOptions
     */
    public function testSetGetAuthenticationOptions(): void
    {
        $options = $this->getMockBuilder(AuthenticationOptionsInterface::class)
            ->getMock();
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getAuthenticationOptions());
    }
}
