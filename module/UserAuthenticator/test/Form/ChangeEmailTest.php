<?php

namespace UserAuthenticatorTest\Form;

use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\ChangeEmail as Form;
use UserAuthenticator\Options\AuthenticationOptionsInterface;

class ChangeEmailTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Form\ChangeEmail::__construct
     */
    public function testConstruct(): void
    {
        $options = $this->getMockBuilder(AuthenticationOptionsInterface::class)
            ->getMock();

        $form = new Form(null, $options);

        $elements = $form->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('newIdentity', $elements);
        $this->assertArrayHasKey('newIdentityVerify', $elements);
        $this->assertArrayHasKey('credential', $elements);
        $this->assertArrayHasKey('csrf', $elements);
    }

    /**
     * @covers UserAuthenticator\Form\ChangeEmail::getAuthenticationOptions
     * @covers UserAuthenticator\Form\ChangeEmail::setAuthenticationOptions
     */
    public function testSetGetAuthenticationOptions(): void
    {
        $options = $this->getMockBuilder(AuthenticationOptionsInterface::class)
            ->getMock();
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getAuthenticationOptions());
    }
}
