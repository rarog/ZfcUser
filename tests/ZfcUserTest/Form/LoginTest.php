<?php

namespace ZfcUserTest\Form;

use PHPUnit\Framework\TestCase;
use ZfcUser\Form\Login as Form;
use ZfcUser\Options\AuthenticationOptionsInterface;

class LoginTest extends TestCase
{
    /**
     * @covers ZfcUser\Form\Login::__construct
     * @dataProvider providerTestConstruct
     */
    public function testConstruct($authIdentityFields = []): void
    {
        $options = $this->getMockBuilder(AuthenticationOptionsInterface::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue($authIdentityFields));

        $form = new Form(null, $options);

        $elements = $form->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('credential', $elements);

        $expectedLabel = '';
        if (count($authIdentityFields) > 0) {
            foreach ($authIdentityFields as $field) {
                $expectedLabel .= ($expectedLabel == '') ? '' : ' or ';
                $expectedLabel .= ucfirst($field);
                $this->assertStringContainsString(ucfirst($field), $elements['identity']->getLabel());
            }
        }

        $this->assertEquals($expectedLabel, $elements['identity']->getLabel());
    }

    /**
     * @covers ZfcUser\Form\Login::getAuthenticationOptions
     * @covers ZfcUser\Form\Login::setAuthenticationOptions
     */
    public function testSetGetAuthenticationOptions(): void
    {
        $options = $this->getMockBuilder(AuthenticationOptionsInterface::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue([]));
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getAuthenticationOptions());
    }

    public function providerTestConstruct(): array
    {
        return [
            [[]],
            [['email']],
            [['username','email']],
        ];
    }
}
