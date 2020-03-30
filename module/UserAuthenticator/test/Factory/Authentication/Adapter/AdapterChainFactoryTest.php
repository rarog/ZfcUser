<?php

namespace UserAuthenticatorTest\Factory\Authentication\Adapter;

use Laminas\EventManager\EventManager;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Authentication\Adapter\AbstractAdapter;
use UserAuthenticator\Authentication\Adapter\AdapterChain;
use UserAuthenticator\Authentication\Adapter\Exception\OptionsNotFoundException;
use UserAuthenticator\Factory\Authentication\Adapter\AdapterChainFactory;
use UserAuthenticator\Options\ModuleOptions;

class AdapterChainFactoryTest extends TestCase
{
    /**
     * The object to be tested.
     *
     * @var AdapterChainFactory
     */
    protected $factory;

    /**
     * @var \Laminas\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \UserAuthenticator\Options\ModuleOptions
     */
    protected $options;

    /**
     * @var \Laminas\EventManager\EventManagerInterface
     */
    protected $eventManager;


    protected $serviceLocatorArray;

    public function helperServiceLocator($index)
    {
        return $this->serviceLocatorArray[$index];
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();

        $this->options = $this->getMockBuilder(ModuleOptions::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback([$this, 'helperServiceLocator']));

        $this->eventManager = $this->getMockBuilder(EventManager::class)
            ->getMock();

        $this->serviceLocatorArray = [
            'zfcuser_module_options' => $this->options,
            'EventManager' => $this->eventManager,
        ];

        $this->factory = new AdapterChainFactory();
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->factory);
        unset($this->eventManager);
        unset($this->serviceLocatorArray);
        unset($this->options);
        unset($this->serviceLocator);
    }

    /**
     * @covers \UserAuthenticator\Factory\Authentication\Adapter\AdapterChainFactory::__invoke
     */
    public function testInvoke(): void
    {
        $adapter = [
            'adapter1' => $this->getMockBuilder(AbstractAdapter::class)
                ->onlyMethods(['authenticate'])
                ->addMethods(['logout'])
                ->getMock(),
            'adapter2' => $this->getMockBuilder(AbstractAdapter::class)
                ->onlyMethods(['authenticate'])
                ->addMethods(['logout'])
                ->getMock(),
        ];
        $adapterNames = [
            100 => 'adapter1',
            200 => 'adapter2'
        ];

        $this->serviceLocatorArray = array_merge($this->serviceLocatorArray, $adapter);

        $this->options->expects($this->once())
            ->method('getAuthAdapters')
            ->will($this->returnValue($adapterNames));

        $adapterChain = $this->factory->__invoke($this->serviceLocator, AdapterChain::class);

        $this->assertInstanceOf(AdapterChain::class, $adapterChain);
    }

    /**
     * @covers \UserAuthenticator\Factory\Authentication\Adapter\AdapterChainFactory::setOptions
     * @covers \UserAuthenticator\Factory\Authentication\Adapter\AdapterChainFactory::getOptions
     */
    public function testGetOptionWithSetter(): void
    {
        $this->factory->setOptions($this->options);

        $options = $this->factory->getOptions();

        $this->assertInstanceOf(ModuleOptions::class, $options);
        $this->assertSame($this->options, $options);

        $options2 = clone $this->options;
        $this->factory->setOptions($options2);
        $options = $this->factory->getOptions();

        $this->assertInstanceOf(ModuleOptions::class, $options);
        $this->assertNotSame($this->options, $options);
        $this->assertSame($options2, $options);
    }

    /**
     * @covers \UserAuthenticator\Factory\Authentication\Adapter\AdapterChainFactory::getOptions
     */
    public function testGetOptionWithLocator(): void
    {
        $options = $this->factory->getOptions($this->serviceLocator);

        $this->assertInstanceOf(ModuleOptions::class, $options);
        $this->assertSame($this->options, $options);
    }

    /**
     * @covers \UserAuthenticator\Factory\Authentication\Adapter\AdapterChainFactory::getOptions
     */
    public function testGetOptionFailing(): void
    {
        $this->expectException(OptionsNotFoundException::class);
        $this->factory->getOptions();
    }
}
