<?php

namespace ZfcUser\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ZfcUser\Form\ChangeEmail;
use ZfcUser\Form\ChangeEmailFilter;
use ZfcUser\Validator\NoRecordExists;

class ChangeEmailFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $options = $serviceManager->get('zfcuser_module_options');
        $form = new ChangeEmail(null, $options);

        $form->setInputFilter(new ChangeEmailFilter(
            $options,
            new NoRecordExists([
                'mapper' => $serviceManager->get('zfcuser_user_mapper'),
                'key'    => 'email'
            ])
        ));

        return $form;
    }
}
