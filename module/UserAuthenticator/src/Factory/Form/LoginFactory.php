<?php

namespace UserAuthenticator\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Form\Login;
use UserAuthenticator\Form\LoginFilter;
use UserAuthenticator\Options\ModuleOptions;

class LoginFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $options = $serviceManager->get(ModuleOptions::class);
        $form = new Login(null, $options);

        $form->setInputFilter(new LoginFilter($options));

        return $form;
    }
}
