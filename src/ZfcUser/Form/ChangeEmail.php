<?php

namespace ZfcUser\Form;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use ZfcUser\Options\AuthenticationOptionsInterface;

class ChangeEmail extends ProvidesEventsForm
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
            'name' => 'newIdentity',
            'type' => Text::class,
            'options' => [
                'label' => 'New Email',
            ],
            'attributes' => [
                'id' => 'newIdentity',
            ],
        ]);

        $this->add([
            'name' => 'newIdentityVerify',
            'type' => Text::class,
            'options' => [
                'label' => 'Verify New Email',
            ],
            'attributes' => [
                'id' => 'newIdentityVerify',
            ],
        ]);

        $this->add([
            'name' => 'credential',
            'type' => Password::class,
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'id' => 'credential',
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
     * @return ChangeEmail
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
