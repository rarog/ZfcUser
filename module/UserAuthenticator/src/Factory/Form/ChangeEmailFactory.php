<?php

namespace UserAuthenticator\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Form\ChangeEmail;
use UserAuthenticator\Form\ChangeEmailFilter;
use UserAuthenticator\Mapper\User;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Validator\NoRecordExists;

class ChangeEmailFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $options = $serviceManager->get(ModuleOptions::class);
        $form = new ChangeEmail(null, $options);

        $form->setInputFilter(new ChangeEmailFilter(
            $options,
            new NoRecordExists([
                'mapper' => $serviceManager->get(User::class),
                'key'    => 'email'
            ])
        ));

        return $form;
    }
}
