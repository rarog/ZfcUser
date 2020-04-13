<?php

namespace UserAuthenticator\Form;

use Laminas\Form\Element\Button;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Text;

class LoginForm extends AbstractModuleOptionsForm
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
        foreach ($this->moduleOptions->getAuthIdentityFields() as $mode) {
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
                    'timeout' => $this->moduleOptions->getLoginFormTimeout(),
                ],
            ],
        ]);

        if ($this->moduleOptions->getUseLoginFormCaptcha()) {
            $this->add([
                'name' => 'captcha',
                'type' => Captcha::class,
                'options' => [
                    'label' => 'Please type the following text',
                    'captcha' => $this->moduleOptions->getFormCaptchaOptions(),
                ],
            ]);
        }

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
}
