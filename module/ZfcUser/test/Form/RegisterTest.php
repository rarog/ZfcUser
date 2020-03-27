<?php

namespace ZfcUserTest\Form;

use Laminas\Captcha\AbstractAdapter;
use PHPUnit\Framework\TestCase;
use ZfcUser\Form\Register as Form;
use ZfcUser\Options\RegistrationOptionsInterface;
use ReflectionProperty;

class RegisterTest extends TestCase
{
    /**
     * @dataProvider providerTestConstruct
     */
    public function testConstruct($useCaptcha = false): void
    {
        $options = $this->getMockBuilder(RegistrationOptionsInterface::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue(false));
        $options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue(false));
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

        $this->assertArrayNotHasKey('userId', $elements);
        $this->assertArrayNotHasKey('username', $elements);
        $this->assertArrayNotHasKey('display_name', $elements);
        $this->assertArrayHasKey('email', $elements);
        $this->assertArrayHasKey('password', $elements);
        $this->assertArrayHasKey('passwordVerify', $elements);
        $this->assertArrayHasKey('csrf', $elements);

        if ($useCaptcha) {
            $this->assertArrayHasKey('captcha', $elements);
        } else {
            $this->assertArrayNotHasKey('captcha', $elements);
        }
    }

    public function providerTestConstruct(): array
    {
        return [
            [true],
            [false]
        ];
    }

    public function testSetGetRegistrationOptions(): void
    {
        $options = $this->getMockBuilder(RegistrationOptionsInterface::class)
            ->getMock();
        $options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue(false));
        $options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue(false));
        $options->expects($this->any())
            ->method('getUseRegistrationFormCaptcha')
            ->will($this->returnValue(false));
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getRegistrationOptions());

        $optionsNew = $this->getMockBuilder(RegistrationOptionsInterface::class)
            ->getMock();
        $form->setRegistrationOptions($optionsNew);
        $this->assertSame($optionsNew, $form->getRegistrationOptions());
    }

    /**
     *
     * @param mixed $objectOrClass
     * @param string $property
     * @param mixed $value = null
     * @return \ReflectionProperty
     */
    public function helperMakePropertyAccessable($objectOrClass, $property, $value = null): ReflectionProperty
    {
        $reflectionProperty = new ReflectionProperty($objectOrClass, $property);
        $reflectionProperty->setAccessible(true);

        if ($value !== null) {
            $reflectionProperty->setValue($objectOrClass, $value);
        }
        return $reflectionProperty;
    }
}
