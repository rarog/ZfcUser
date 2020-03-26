<?php

namespace ZfcUserTest\Form;

use PHPUnit\Framework\TestCase;
use ZfcUser\Form\ChangeEmail as Form;
use ZfcUser\Options\AuthenticationOptionsInterface;

class ChangeEmailTest extends TestCase
{
    /**
     * @covers ZfcUser\Form\ChangeEmail::__construct
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
     * @covers ZfcUser\Form\ChangeEmail::getAuthenticationOptions
     * @covers ZfcUser\Form\ChangeEmail::setAuthenticationOptions
     */
    public function testSetGetAuthenticationOptions(): void
    {
        $options = $this->getMockBuilder(AuthenticationOptionsInterface::class)
            ->getMock();
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getAuthenticationOptions());
    }
}
