<?php

namespace UserAuthenticator\Form;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;

class ChangePasswordForm extends AbstractModuleOptionsForm
{
    /**
     * {@inheritDoc}
     * @see \UserAuthenticator\Form\AbstractModuleOptionsForm::__construct()
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

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
                    'timeout' => $this->moduleOptions->getLoginFormTimeout(),
                ],
            ],
        ]);
    }
}
