<?php

namespace UserAuthenticator\Form;

use Laminas\Form\Element\Button;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Text;

class Base extends ProvidesEventsForm
{
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->add([
            'name' => 'username',
            'type' => Text::class,
            'options' => [
                'label' => 'Username',
            ],
            'attributes' => [
                'id' => 'username',
            ],
        ]);

        $this->add([
            'name' => 'email',
            'type' => Text::class,
            'options' => [
                'label' => 'Email',
            ],
            'attributes' => [
                'id' => 'email',
            ],
        ]);

        $this->add([
            'name' => 'display_name',
            'type' => Text::class,
            'options' => [
                'label' => 'Display Name',
            ],
            'attributes' => [
                'id' => 'display_name',
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => Password::class,
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'id' => 'password',
            ],
        ]);

        $this->add([
            'name' => 'passwordVerify',
            'type' => Password::class,
            'options' => [
                'label' => 'Password Verify',
            ],
            'attributes' => [
                'id' => 'passwordVerify',
            ],
        ]);

        $submitElement = new Button('submit');
        $submitElement
            ->setLabel('Submit')
            ->setAttributes([
                'type'  => 'submit',
            ]);

        $this->add($submitElement, [
            'priority' => -100,
        ]);

        $this->add([
            'name' => 'userId',
            'type' => Hidden::class,
        ]);
    }
}
