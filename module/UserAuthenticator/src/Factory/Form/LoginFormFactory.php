<?php

namespace UserAuthenticator\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Form\LoginFilter;
use UserAuthenticator\Form\LoginForm;
use UserAuthenticator\Options\ModuleOptions;

class LoginFormFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $moduleOptions = $container->get(ModuleOptions::class);
        $form = new LoginForm(
            null,
            [
                'module_options' => $moduleOptions,
            ]
        );

        $form->setInputFilter(new LoginFilter($moduleOptions));

        return $form;
    }
}
