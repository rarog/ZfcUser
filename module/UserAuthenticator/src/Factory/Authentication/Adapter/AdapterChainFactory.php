<?php

namespace UserAuthenticator\Factory\Authentication\Adapter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Authentication\Adapter\AdapterChain;
use UserAuthenticator\Authentication\Adapter\Exception\OptionsNotFoundException;
use UserAuthenticator\Options\ModuleOptions;

class AdapterChainFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $chain = new AdapterChain();
        $chain->setEventManager($serviceLocator->get('EventManager'));

        $options = $this->getOptions($serviceLocator);

        //iterate and attach multiple adapters and events if offered
        foreach ($options->getAuthAdapters() as $priority => $adapterName) {
            $adapter = $serviceLocator->get($adapterName);

            if (is_callable([$adapter, 'authenticate'])) {
                $chain->getEventManager()->attach('authenticate', [$adapter, 'authenticate'], $priority);
            }

            if (is_callable([$adapter, 'logout'])) {
                $chain->getEventManager()->attach('logout', [$adapter, 'logout'], $priority);
            }
        }

        return $chain;
    }

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * set options
     *
     * @param ModuleOptions $options
     * @return AdapterChainFactory
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * get options
     *
     * @param ContainerInterface $serviceLocator (optional) Service Locator
     * @return ModuleOptions $options
     * @throws OptionsNotFoundException If options tried to retrieve without being set but no SL was provided
     */
    public function getOptions(ContainerInterface $serviceLocator = null)
    {
        if (! $this->options) {
            if (! $serviceLocator) {
                throw new OptionsNotFoundException(
                    'Options were tried to retrieve but not set ' .
                    'and no service locator was provided'
                );
            }

            $this->setOptions($serviceLocator->get(ModuleOptions::class));
        }

        return $this->options;
    }
}
