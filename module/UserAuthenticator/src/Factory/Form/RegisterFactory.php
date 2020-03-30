<?php

namespace UserAuthenticator\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Form\Register;
use UserAuthenticator\Form\RegisterFilter;
use UserAuthenticator\Validator\NoRecordExists;

class RegisterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $options = $serviceManager->get('zfcuser_module_options');
        $form = new Register(null, $options);

        //$form->setCaptchaElement($sm->get('zfcuser_captcha_element'));
        $form->setHydrator($serviceManager->get('zfcuser_register_form_hydrator'));
        $form->setInputFilter(new RegisterFilter(
            new NoRecordExists([
                'mapper' => $serviceManager->get('zfcuser_user_mapper'),
                'key'    => 'email'
            ]),
            new NoRecordExists([
                'mapper' => $serviceManager->get('zfcuser_user_mapper'),
                'key'    => 'username'
            ]),
            $options
        ));

        return $form;
    }
}
