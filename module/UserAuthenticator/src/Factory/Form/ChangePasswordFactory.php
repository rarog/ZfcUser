<?php

namespace UserAuthenticator\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Form\ChangePassword;
use UserAuthenticator\Form\ChangePasswordFilter;
use UserAuthenticator\Options\ModuleOptions;

class ChangePasswordFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get(ModuleOptions::class);
        $form = new ChangePassword(null, $options);

        $form->setInputFilter(new ChangePasswordFilter($options));

        return $form;
    }
}
