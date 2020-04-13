<?php

namespace UserAuthenticator\Form;

use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;

class RegisterForm extends AbstractModuleOptionsForm
{
    /**
     * {@inheritDoc}
     * @see \UserAuthenticator\Form\AbstractModuleOptionsForm::__construct()
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        if ($this->moduleOptions->getEnableUsername()) {
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
        }

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

        if ($this->moduleOptions->getEnableDisplayName()) {
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
        }

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

        $this->add(
            [
                'name' => 'submit',
                'type' => Submit::class,
                'attributes' => [
                    'value' => 'RegisterForm',
                ],
            ],
            [
                'priority' => -100,
            ]
        );

        $this->add([
            'name' => 'csrf',
            'type' => Csrf::class,
            'options' => [
                'csrf_options' => [
                    'timeout' => $this->moduleOptions->getUserFormTimeout(),
                ],
            ],
        ]);

        if ($this->moduleOptions->getUseRegistrationFormCaptcha()) {
            $this->add([
                'name' => 'captcha',
                'type' => Captcha::class,
                'options' => [
                    'label' => 'Please type the following text',
                    'captcha' => $this->moduleOptions->getFormCaptchaOptions(),
                ],
            ]);
        }
    }
}
