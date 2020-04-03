<?php

namespace UserAuthenticator\Factory\Authentication\Adapter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Authentication\Adapter\AdapterChain;
use UserAuthenticator\Options\ModuleOptions;

class AdapterChainFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $chain = new AdapterChain();
        $chain->setEventManager($container->get('EventManager'));

        $options = $container->get(ModuleOptions::class);

        //iterate and attach multiple adapters and events if offered
        foreach ($options->getAuthAdapters() as $priority => $adapterName) {
            $adapter = $container->get($adapterName);

            if (is_callable([$adapter, 'authenticate'])) {
                $chain->getEventManager()->attach('authenticate', [$adapter, 'authenticate'], $priority);
            }

            if (is_callable([$adapter, 'logout'])) {
                $chain->getEventManager()->attach('logout', [$adapter, 'logout'], $priority);
            }
        }

        return $chain;
    }
}
