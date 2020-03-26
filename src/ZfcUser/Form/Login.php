<?php

namespace ZfcUser\Form;

use Laminas\Form\Element\Button;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Text;
use ZfcUser\Options\AuthenticationOptionsInterface;
use Laminas\Form\Element\Csrf;

class Login extends ProvidesEventsForm
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
            'type' => Text::class,
            'options' => [
                'label' => '',
            ],
            'attributes' => [
                'id' => 'identity',
            ],
        ]);

        $emailElement = $this->get('identity');
        $label = $emailElement->getLabel('label');
        // @TODO: make translation-friendly
        foreach ($this->getAuthenticationOptions()->getAuthIdentityFields() as $mode) {
            $label = (! empty($label) ? $label . ' or ' : '') . ucfirst($mode);
        }
        $emailElement->setLabel($label);
        //
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
            'name' => 'csrf',
            'type' => Csrf::class,
            'options' => [
                'csrf_options' => [
                    'timeout' => $this->getAuthenticationOptions()->getLoginFormTimeout(),
                ],
            ],
        ]);

        // @todo: Fix this
        // 1) getValidator() is a protected method
        // 2) i don't believe the login form is actually being validated by the login action
        // (but keep in mind we don't want to show invalid username vs invalid password or
        // anything like that, it should just say "login failed" without any additional info)

        $submitElement = new Button('submit');
        $submitElement
            ->setLabel('Sign In')
            ->setAttributes([
                'type' => 'submit',
            ]);

        $this->add($submitElement, [
            'priority' => -100,
        ]);
    }

    /**
     * Set Authentication-related Options
     *
     * @param AuthenticationOptionsInterface $authOptions
     * @return Login
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
