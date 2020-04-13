<?php

namespace UserAuthenticator\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Form\ChangeEmailFilter;
use UserAuthenticator\Form\ChangeEmailForm;
use UserAuthenticator\Mapper\User;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Validator\NoRecordExists;

class ChangeEmailFormFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $moduleOptions = $container->get(ModuleOptions::class);
        $form = new ChangeEmailForm(
            null,
            [
                'module_options' => $moduleOptions,
            ]
        );

        $form->setInputFilter(new ChangeEmailFilter(
            $moduleOptions,
            new NoRecordExists([
                'mapper' => $container->get(User::class),
                'key'    => 'email'
            ])
        ));

        return $form;
    }
}
