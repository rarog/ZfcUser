<?php

namespace ZfcUserTest\Form;

use PHPUnit\Framework\TestCase;
use ZfcUser\Form\ChangePassword as Form;
use ZfcUser\Options\AuthenticationOptionsInterface;

class ChangePasswordTest extends TestCase
{
    /**
     * @covers ZfcUser\Form\ChangePassword::__construct
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
     * @covers ZfcUser\Form\ChangePassword::getAuthenticationOptions
     * @covers ZfcUser\Form\ChangePassword::setAuthenticationOptions
     */
    public function testSetGetAuthenticationOptions(): void
    {
        $options = $this->getMockBuilder(AuthenticationOptionsInterface::class)
            ->getMock();
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getAuthenticationOptions());
    }
}
