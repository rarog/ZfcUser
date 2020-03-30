<?php

namespace UserAuthenticator\InputFilter;

use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\InputFilter\InputFilter;

class ProvidesEventsInputFilter extends InputFilter
{
    use EventManagerAwareTrait;
}
