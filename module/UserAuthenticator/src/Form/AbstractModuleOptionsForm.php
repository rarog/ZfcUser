<?php

namespace UserAuthenticator\Form;

use Laminas\Form\Form;
use UserAuthenticator\Options\ModuleOptions;
use InvalidArgumentException;

abstract class AbstractModuleOptionsForm extends Form
{
    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * {@inheritDoc}
     * @see \Laminas\Form\Fieldset::__construct()
     * @throws InvalidArgumentException
     */
    public function __construct($name = null, $options = [])
    {
        if (
            ! is_array($options) ||
            ! array_key_exists('module_options', $options) ||
            ! $options['module_options'] instanceof ModuleOptions
        ) {
            throw new InvalidArgumentException('No module options were passed to the constructor.');
        }
        $this->moduleOptions = $options['module_options'];

        parent::__construct($name, $options);
    }
}
