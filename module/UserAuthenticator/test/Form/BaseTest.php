<?php

namespace UserAuthenticatorTest\Form;

use PHPUnit\Framework\TestCase;
use UserAuthenticator\Form\Base as Form;

class BaseTest extends TestCase
{
    public function testConstruct(): void
    {
        $form = new Form();

        $elements = $form->getElements();

        $this->assertArrayHasKey('username', $elements);
        $this->assertArrayHasKey('email', $elements);
        $this->assertArrayHasKey('display_name', $elements);
        $this->assertArrayHasKey('password', $elements);
        $this->assertArrayHasKey('passwordVerify', $elements);
        $this->assertArrayHasKey('submit', $elements);
        $this->assertArrayHasKey('userId', $elements);
    }
}
