<?php

namespace UserAuthenticator\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\Identical;
use UserAuthenticator\Options\AuthenticationOptionsInterface;

class ChangeEmailFilter extends InputFilter
{
    protected $emailValidator;

    public function __construct(AuthenticationOptionsInterface $options, $emailValidator)
    {
        $this->emailValidator = $emailValidator;

        $identityParams = [
            'name' => 'identity',
            'required' => true,
            'validators' => [],
        ];

        $identityFields = $options->getAuthIdentityFields();
        if ($identityFields == ['email']) {
            $validators = ['name' => EmailAddress::class];
            array_push($identityParams['validators'], $validators);
        }

        $this->add($identityParams);

        $this->add([
            'name' => 'newIdentity',
            'required' => true,
            'validators' => [
                [
                    'name' => EmailAddress::class,
                ],
                $this->emailValidator,
            ],
        ]);

        $this->add([
            'name' => 'newIdentityVerify',
            'required' => true,
            'validators' => [
                [
                    'name' => Identical::class,
                    'options' => [
                        'token' => 'newIdentity'
                    ]
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
}
