<?php

namespace UserAuthenticator\Form;

use Laminas\Form\Element\Captcha;
use UserAuthenticator\Options\RegistrationOptionsInterface;
use Laminas\Form\Element\Csrf;

class Register extends Base
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

        $this->remove('userId');
        if (! $this->getRegistrationOptions()->getEnableUsername()) {
            $this->remove('username');
        }
        if (! $this->getRegistrationOptions()->getEnableDisplayName()) {
            $this->remove('display_name');
        }
        $this->get('submit')->setLabel('Register');
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
