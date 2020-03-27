<?php

namespace ZfcUser\Form;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use ZfcUser\Options\AuthenticationOptionsInterface;

class ChangePassword extends ProvidesEventsForm
{
    /**
     * @var AuthenticationOptionsInterface
     */
    protected $authOptions;

    public function __construct($name, AuthenticationOptionsInterface $options)
    {
        $this->setAuthenticationOptions($options);

        parent::__construct($name);

        $this->add([
            'name' => 'identity',
            'type' => Hidden::class,
            'options' => [
                'label' => '',
            ],
            'attributes' => [
                'id' => 'identity',
            ],
        ]);

        $this->add([
            'name' => 'credential',
            'type' => Password::class,
            'options' => [
                'label' => 'Current Password',
            ],
            'attributes' => [
                'id' => 'credential',
            ],
        ]);

        $this->add([
            'name' => 'newCredential',
            'type' => Password::class,
            'options' => [
                'label' => 'New Password',
            ],
            'attributes' => [
                'id' => 'newCredential',
            ],
        ]);

        $this->add([
            'name' => 'newCredentialVerify',
            'type' => Password::class,
            'options' => [
                'label' => 'Verify New Password',
            ],
            'attributes' => [
                'id' => 'newCredentialVerify',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Submit::class,
            'attributes' => [
                'value' => 'Submit',
            ],
        ]);

        $this->add([
            'name' => 'csrf',
            'type' => Csrf::class,
            'options' => [
                'csrf_options' => [
                    'timeout' => $this->getAuthenticationOptions()->getLoginFormTimeout(),
                ],
            ],
        ]);
    }

    /**
     * Set Authentication-related Options
     *
     * @param AuthenticationOptionsInterface $authOptions
     * @return ChangePassword
     */
    public function setAuthenticationOptions(AuthenticationOptionsInterface $authOptions)
    {
        $this->authOptions = $authOptions;

        return $this;
    }

    /**
     * Get Authentication-related Options
     *
     * @return AuthenticationOptionsInterface
     */
    public function getAuthenticationOptions()
    {
        return $this->authOptions;
    }
}
