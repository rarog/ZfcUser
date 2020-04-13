<?php

namespace UserAuthenticatorTest\Form;

use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\ChangeEmailForm;
use UserAuthenticator\Options\ModuleOptions;

class ChangeEmailFormTest extends TestCase
{
    /**
     * @covers UserAuthenticator\Form\ChangeEmailForm::__construct
     */
    public function testConstruct(): void
    {
        $moduleOptions = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();

        $changeEmailForm = new ChangeEmailForm(
            null,
            [
                'module_options' => $moduleOptions,
            ]
        );

        $elements = $changeEmailForm->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('newIdentity', $elements);
        $this->assertArrayHasKey('newIdentityVerify', $elements);
        $this->assertArrayHasKey('credential', $elements);
        $this->assertArrayHasKey('csrf', $elements);
    }
}
