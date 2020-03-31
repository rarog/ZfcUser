<?php

namespace UserAuthenticator\Form;

use Laminas\Form\Form;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use UserAuthenticator\Options\RegistrationOptionsInterface;

class Register extends Form
{
    /**
     * @var RegistrationOptionsInterface
     */
    protected $registrationOptions;

    /**
     * @param string|null $name
     * @param RegistrationOptionsInterface $options
     */
    public function __construct($name, RegistrationOptionsInterface $options)
    {
        $this->setRegistrationOptions($options);

        parent::__construct($name);

        if ($this->getRegistrationOptions()->getEnableUsername()) {
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

        if ($this->getRegistrationOptions()->getEnableDisplayName()) {
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

        $this->add([
            'name' => 'submit',
            'type' => Submit::class,
            'attributes' => [
                'value' => 'Register',
            ],
        ],
        [
            'priority' => -100,
        ]);

        $this->add([
            'name' => 'csrf',
            'type' => Csrf::class,
            'options' => [
                'csrf_options' => [
                    'timeout' => $this->getRegistrationOptions()->getUserFormTimeout(),
                ],
            ],
        ]);

        if ($this->getRegistrationOptions()->getUseRegistrationFormCaptcha()) {
            $this->add([
                'name' => 'captcha',
                'type' => Captcha::class,
                'options' => [
                    'label' => 'Please type the following text',
                    'captcha' => $this->getRegistrationOptions()->getFormCaptchaOptions(),
                ],
            ]);
        }
    }

    /**
     * Set Registration Options
     *
     * @param RegistrationOptionsInterface $registrationOptions
     * @return Register
     */
    public function setRegistrationOptions(RegistrationOptionsInterface $registrationOptions)
    {
        $this->registrationOptions = $registrationOptions;
        return $this;
    }

    /**
     * Get Registration Options
     *
     * @return RegistrationOptionsInterface
     */
    public function getRegistrationOptions()
    {
        return $this->registrationOptions;
    }
}
