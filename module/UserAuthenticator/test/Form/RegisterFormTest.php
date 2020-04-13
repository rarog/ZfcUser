<?php

namespace UserAuthenticatorTest\Form;

use Laminas\Captcha\AbstractAdapter;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\RegisterForm;
use UserAuthenticator\Options\ModuleOptions;

class RegisterFormTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Form\RegisterForm::__construct
     * @dataProvider providerTestConstruct
     */
    public function testConstruct(bool $enableUsername, bool $enableDisplayName, bool $useCaptcha): void
    {
        $moduleOptions = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $moduleOptions->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue($enableUsername));
        $moduleOptions->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue($enableDisplayName));
        $moduleOptions->expects($this->any())
            ->method('getUseRegistrationFormCaptcha')
            ->will($this->returnValue($useCaptcha));
        if ($useCaptcha && class_exists(AbstractAdapter::class)) {
            $captcha = $this->getMockForAbstractClass(AbstractAdapter::class);

            $moduleOptions->expects($this->once())
                ->method('getFormCaptchaOptions')
                ->will($this->returnValue($captcha));
        }

        $registerForm = new RegisterForm(
            null,
            [
                'module_options' => $moduleOptions,
            ]
        );

        $elements = $registerForm->getElements();

        if ($enableUsername) {
            $this->assertArrayHasKey('username', $elements);
        } else {
            $this->assertArrayNotHasKey('username', $elements);
        }
        if ($enableDisplayName) {
            $this->assertArrayHasKey('display_name', $elements);
        } else {
            $this->assertArrayNotHasKey('display_name', $elements);
        }
        $this->assertArrayHasKey('email', $elements);
        $this->assertArrayHasKey('password', $elements);
        $this->assertArrayHasKey('passwordVerify', $elements);
        $this->assertArrayHasKey('csrf', $elements);
        $this->assertArrayHasKey('submit', $elements);
        if ($useCaptcha) {
            $this->assertArrayHasKey('captcha', $elements);
        } else {
            $this->assertArrayNotHasKey('captcha', $elements);
        }
    }

    /**
     * @return array
     */
    public function providerTestConstruct(): array
    {
        return [
            [true, true, true],
            [true, true, false],
            [true, false, true],
            [true, false, false],
            [false, true, true],
            [false, true, false],
            [false, false, true],
            [false, false, false],
        ];
    }
}
