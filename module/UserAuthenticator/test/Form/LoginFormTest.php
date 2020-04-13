<?php

namespace UserAuthenticatorTest\Form;

use Laminas\Captcha\AbstractAdapter;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\LoginForm;
use UserAuthenticator\Options\ModuleOptions;

class LoginFormTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Form\LoginForm::__construct
     * @dataProvider providerTestConstruct
     */
    public function testConstruct(array $authIdentityFields, bool $useCaptcha): void
    {
        $moduleOptions = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $moduleOptions->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue($authIdentityFields));
        $moduleOptions->expects($this->any())
            ->method('getUseLoginFormCaptcha')
            ->will($this->returnValue($useCaptcha));
        if ($useCaptcha && class_exists(AbstractAdapter::class)) {
            $captcha = $this->getMockForAbstractClass(AbstractAdapter::class);

            $moduleOptions->expects($this->once())
                ->method('getFormCaptchaOptions')
                ->will($this->returnValue($captcha));
        }

        $loginForm = new LoginForm(
            null,
            [
                'module_options' => $moduleOptions,
            ]
        );

        $elements = $loginForm->getElements();

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
     * @return array
     */
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
