<?php

namespace UserAuthenticator\Form;

use Laminas\Filter\StringTrim;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\Identical;
use Laminas\Validator\StringLength;
use UserAuthenticator\InputFilter\ProvidesEventsInputFilter;
use UserAuthenticator\Options\RegistrationOptionsInterface;

class RegisterFilter extends ProvidesEventsInputFilter
{
    protected $emailValidator;
    protected $usernameValidator;

    /**
     * @var RegistrationOptionsInterface
     */
    protected $options;

    public function __construct($emailValidator, $usernameValidator, RegistrationOptionsInterface $options)
    {
        $this->setOptions($options);
        $this->emailValidator = $emailValidator;
        $this->usernameValidator = $usernameValidator;

        if ($this->getOptions()->getEnableUsername()) {
            $this->add([
                'name'  => 'username',
                'required' => true,
                'validators' => [
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 3,
                            'max' => 255,
                        ],
                    ],
                    $this->usernameValidator,
                ],
            ]);
        }

        $this->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                [
                    'name' => EmailAddress::class
                ],
                $this->emailValidator
            ],
        ]);

        if ($this->getOptions()->getEnableDisplayName()) {
            $this->add([
                'name' => 'display_name',
                'required' => true,
                'filters' => [
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 3,
                            'max' => 128,
                        ],
                    ],
                ],
            ]);
        }

        $this->add([
            'name' => 'password',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 6,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'passwordVerify',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 6,
                    ],
                ],
                [
                    'name' => Identical::class,
                    'options' => [
                        'token' => 'password',
                    ],
                ],
            ],
        ]);
    }

    public function getEmailValidator()
    {
        return $this->emailValidator;
    }

    public function setEmailValidator($emailValidator)
    {
        $this->emailValidator = $emailValidator;
        return $this;
    }

    public function getUsernameValidator()
    {
        return $this->usernameValidator;
    }

    public function setUsernameValidator($usernameValidator)
    {
        $this->usernameValidator = $usernameValidator;
        return $this;
    }

    /**
     * set options
     *
     * @param RegistrationOptionsInterface $options
     */
    public function setOptions(RegistrationOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * get options
     *
     * @return RegistrationOptionsInterface
     */
    public function getOptions()
    {
        return $this->options;
    }
}
