<?php

namespace UserAuthenticatorTest\Form;

use Laminas\Captcha\AbstractAdapter;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\Register as Form;
use UserAuthenticator\Options\RegistrationOptionsInterface;

class RegisterTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Form\Register::__construct
     * @dataProvider providerTestConstruct
     */
    public function testConstruct(bool $enableUsername, bool $enableDisplayName, bool $useCaptcha): void
    {
        $options = $this->getMockBuilder(RegistrationOptionsInterface::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue($enableUsername));
        $options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue($enableDisplayName));
        $options->expects($this->any())
            ->method('getUseRegistrationFormCaptcha')
            ->will($this->returnValue($useCaptcha));
        if ($useCaptcha && class_exists(AbstractAdapter::class)) {
            $captcha = $this->getMockForAbstractClass(AbstractAdapter::class);

            $options->expects($this->once())
                ->method('getFormCaptchaOptions')
                ->will($this->returnValue($captcha));
        }

        $form = new Form(null, $options);

        $elements = $form->getElements();

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

    /**

     * @covers UserAuthenticator\Form\Register::getRegistrationOptions
     * @covers UserAuthenticator\Form\Register::setRegistrationOptions
     */
    public function testSetGetRegistrationOptions(): void
    {
        $options = $this->getMockBuilder(RegistrationOptionsInterface::class)
            ->getMock();
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getRegistrationOptions());

        $optionsNew = $this->getMockBuilder(RegistrationOptionsInterface::class)
            ->getMock();
        $form->setRegistrationOptions($optionsNew);
        $this->assertSame($optionsNew, $form->getRegistrationOptions());
    }
}
