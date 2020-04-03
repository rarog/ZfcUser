<?php

namespace UserAuthenticatorTest\Factory\Authentication\Adapter;

use Laminas\EventManager\EventManager;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Authentication\Adapter\AbstractAdapter;
use UserAuthenticator\Authentication\Adapter\AdapterChain;
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
            ModuleOptions::class => $this->options,
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
}
