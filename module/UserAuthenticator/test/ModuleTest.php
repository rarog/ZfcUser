<?php

namespace UserAuthenticatorTest;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerPluginProviderInterface;
use Laminas\ModuleManager\Feature\ControllerProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use Laminas\ModuleManager\Feature\ViewHelperProviderInterface;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Module;
use ReflectionClass;

class ModuleTest extends TestCase
{
    /**
     * @var Module
     */
    private $module;

    /**
     * @var ReflectionClass
     */
    private $moduleReflection;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->module = new Module();
        $this->moduleReflection = new ReflectionClass(Module::class);
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->moduleReflection);
        unset($this->module);
    }

    /**
     * @covers UserAuthenticator\Module::getConfig
     */
    public function testImplementsConfigProviderInterface(): void
    {
        $this->assertInstanceOf(ConfigProviderInterface::class, $this->module);
        $this->assertTrue($this->moduleReflection->hasMethod('getConfig'));
        $this->assertIsArray($this->module->getConfig());
    }

    /**
     * @covers UserAuthenticator\Module::getControllerPluginConfig
     */
    public function testImplementsControllerPluginProviderInterface(): void
    {
        $this->assertInstanceOf(ControllerPluginProviderInterface::class, $this->module);
        $this->assertTrue($this->moduleReflection->hasMethod('getControllerPluginConfig'));
        $this->assertIsArray($this->module->getControllerPluginConfig());
    }

    /**
     * @covers UserAuthenticator\Module::getControllerConfig
     */
    public function testImplementsControllerProviderInterface(): void
    {
        $this->assertInstanceOf(ControllerProviderInterface::class, $this->module);
        $this->assertTrue($this->moduleReflection->hasMethod('getControllerConfig'));
        $this->assertIsArray($this->module->getControllerConfig());
    }

    /**
     * @covers UserAuthenticator\Module::getViewHelperConfig
     */
    public function testImplementsCViewHelperProviderInterface(): void
    {
        $this->assertInstanceOf(ViewHelperProviderInterface::class, $this->module);
        $this->assertTrue($this->moduleReflection->hasMethod('getViewHelperConfig'));
        $this->assertIsArray($this->module->getViewHelperConfig());
    }

    /**
     * @covers UserAuthenticator\Module::getServiceConfig
     */
    public function testImplementsServiceProviderInterface(): void
    {
        $this->assertInstanceOf(ServiceProviderInterface::class, $this->module);
        $this->assertTrue($this->moduleReflection->hasMethod('getServiceConfig'));

        $serviceConfig = $this->module->getServiceConfig();
        $this->assertIsArray($serviceConfig);
    }
}
