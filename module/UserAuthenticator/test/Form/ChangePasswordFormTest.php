<?php

namespace UserAuthenticatorTest\Form;

use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\ChangePasswordForm;
use UserAuthenticator\Options\ModuleOptions;

class ChangePasswordFormTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Form\ChangePasswordForm::__construct
     */
    public function testConstruct(): void
    {
        $moduleOptions = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();

        $changePasswordForm = new ChangePasswordForm(
            null,
            [
                'module_options' => $moduleOptions,
            ]
        );

        $elements = $changePasswordForm->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('credential', $elements);
        $this->assertArrayHasKey('newCredential', $elements);
        $this->assertArrayHasKey('newCredentialVerify', $elements);
        $this->assertArrayHasKey('csrf', $elements);
    }
}
