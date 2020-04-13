<?php

namespace UserAuthenticator\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Form\ChangePasswordFilter;
use UserAuthenticator\Form\ChangePasswordForm;
use UserAuthenticator\Options\ModuleOptions;

class ChangePasswordFormFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $moduleOptions = $container->get(ModuleOptions::class);
        $form = new ChangePasswordForm(
            null,
            [
                'module_options' => $moduleOptions,
            ]
        );

        $form->setInputFilter(new ChangePasswordFilter($moduleOptions));

        return $form;
    }
}
