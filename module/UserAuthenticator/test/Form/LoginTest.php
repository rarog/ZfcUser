<?php

namespace UserAuthenticatorTest\Form;

use Laminas\Captcha\AbstractAdapter;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\Login as Form;
use UserAuthenticator\Options\AuthenticationOptionsInterface;

class LoginTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Form\Login::__construct
     * @dataProvider providerTestConstruct
     */
    public function testConstruct(array $authIdentityFields, bool $useCaptcha): void
    {
        $options = $this->getMockBuilder(AuthenticationOptionsInterface::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue($authIdentityFields));
        $options->expects($this->any())
            ->method('getUseLoginFormCaptcha')
            ->will($this->returnValue($useCaptcha));
        if ($useCaptcha && class_exists(AbstractAdapter::class)) {
            $captcha = $this->getMockForAbstractClass(AbstractAdapter::class);

            $options->expects($this->once())
                ->method('getFormCaptchaOptions')
                ->will($this->returnValue($captcha));
        }

        $form = new Form(null, $options);

        $elements = $form->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('credential', $elements);
        $this->assertArrayHasKey('csrf', $elements);

        if ($useCaptcha) {
            $this->assertArrayHasKey('captcha', $elements);
        } else {
            $this->assertArrayNotHasKey('captcha', $elements);
        }

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
     * @covers UserAuthenticator\Form\Login::getAuthenticationOptions
     * @covers UserAuthenticator\Form\Login::setAuthenticationOptions
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
            [[], false],
            [['email'], false],
            [['username','email'], false],
            [[], true],
        ];
    }
}
