<?php

namespace ZfcUser\Form;

use Laminas\Filter\StringTrim;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\StringLength;
use ZfcUser\InputFilter\ProvidesEventsInputFilter;
use ZfcUser\Options\AuthenticationOptionsInterface;

class LoginFilter extends ProvidesEventsInputFilter
{
    public function __construct(AuthenticationOptionsInterface $options)
    {
        $identityParams = [
            'name' => 'identity',
            'required'  => true,
            'validators' => [],
            'filters' => [
                ['name' => StringTrim::class],
            ],
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
    }
}
