<?php

namespace ZfcUser\Form;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\Identical;
use Laminas\Validator\StringLength;
use ZfcUser\Options\AuthenticationOptionsInterface;

class ChangePasswordFilter extends InputFilter
{
    public function __construct(AuthenticationOptionsInterface $options)
    {
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
            'name' => 'credential',
            'required' => true,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 6,
                    ],
                ],
            ],
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ]);

        $this->add([
            'name' => 'newCredential',
            'required' => true,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 6,
                    ],
                ],
            ],
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ]);

        $this->add([
            'name' => 'newCredentialVerify',
            'required' => true,
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
                        'token' => 'newCredential'
                    ]
                ],
            ],
            'filters' => [
                ['name' => StringTrim::class],
            ],
        ]);
    }
}
