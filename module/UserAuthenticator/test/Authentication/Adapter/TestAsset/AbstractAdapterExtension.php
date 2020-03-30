<?php

namespace UserAuthenticatorTest\Authentication\Adapter\TestAsset;

use Laminas\EventManager\EventInterface;
use UserAuthenticator\Authentication\Adapter\AbstractAdapter;
use UserAuthenticator\Authentication\Adapter\AdapterChainEvent;

class AbstractAdapterExtension extends AbstractAdapter
{
    public function authenticate(AdapterChainEvent $e)
    {
    }
}
