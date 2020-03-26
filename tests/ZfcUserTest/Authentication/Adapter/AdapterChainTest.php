<?php

namespace ZfcUserTest\Authentication\Adapter;

use Laminas\Authentication\Result;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ResponseCollection;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Stdlib\RequestInterface;
use Laminas\Stdlib\ResponseInterface;
use PHPUnit\Framework\TestCase;
use ZfcUser\Authentication\Adapter\AdapterChain;
use ZfcUser\Authentication\Adapter\AdapterChainEvent;
use ZfcUser\Authentication\Adapter\ChainableAdapter;
use ZfcUser\Authentication\Storage\Db;
use ZfcUser\Exception\AuthenticationEventException;

class AdapterChainTest extends TestCase
{
    /**
     * The object to be tested.
     *
     * @var AdapterChain
     */
    protected $adapterChain;

    /**
     * Mock event manager.
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|EventManagerInterface
     */
    protected $eventManager;

    /**
     * Mock event manager.
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|SharedEventManagerInterface
     */
    protected $sharedEventManager;

    /**
     * For tests where an event is required.
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|EventInterface
     */
    protected $event;

    /**
     * Used when testing prepareForAuthentication.
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|RequestInterface
     */
    protected $request;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->event = null;
        $this->request = null;

        $this->adapterChain = new AdapterChain();

        $this->sharedEventManager = $this->getMockBuilder(SharedEventManagerInterface::class)
            ->getMock();

        $this->eventManager = $this->getMockBuilder(EventManagerInterface::class)
            ->getMock();
        $this->eventManager->expects($this->any())->method('getSharedManager')
            ->will($this->returnValue($this->sharedEventManager));
        $this->eventManager->expects($this->any())->method('setIdentifiers');

        $this->adapterChain->setEventManager($this->eventManager);
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->eventManager);
        unset($this->sharedEventManager);
        unset($this->adapterChain);
        unset($this->request);
        unset($this->event);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\AdapterChain::authenticate
     */
    public function testAuthenticate(): void
    {
        $event = $this->getMockBuilder(AdapterChainEvent::class)
            ->getMock();
        $event->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue(123));
        $event->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue('identity'));
        $event->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue([]));

        $this->sharedEventManager->expects($this->once())
            ->method('getListeners')
            ->with($this->equalTo(['authenticate']), $this->equalTo('authenticate'))
            ->will($this->returnValue([]));

        $this->adapterChain->setEvent($event);
        $result = $this->adapterChain->authenticate();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($result->getIdentity(), 'identity');
        $this->assertEquals($result->getMessages(), []);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\AdapterChain::resetAdapters
     */
    public function testResetAdapters(): void
    {
        $listeners = [];

        for ($i = 1; $i <= 3; $i++) {
            $storage = $this->getMockBuilder(Db::class)
                ->getMock();
            $storage->expects($this->once())
                ->method('clear');

            $adapter = $this->getMockBuilder(ChainableAdapter::class)
                ->getMock();
            $adapter->expects($this->once())
                ->method('getStorage')
                ->will($this->returnValue($storage));

            $callback = [$adapter, 'authenticate'];
            $listeners[] = $callback;
        }

        $this->sharedEventManager->expects($this->once())
            ->method('getListeners')
            ->with($this->equalTo(['authenticate']), $this->equalTo('authenticate'))
            ->will($this->returnValue($listeners));

        $result = $this->adapterChain->resetAdapters();

        $this->assertInstanceOf(AdapterChain::class, $result);
    }

    /**
     * Get through the first part of SetUpPrepareForAuthentication
     */
    protected function setUpPrepareForAuthentication(): ResponseCollection
    {
        $this->request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $this->event = $this->getMockBuilder(AdapterChainEvent::class)
            ->getMock();

        $this->event->expects($this->once())->method('setRequest')->with($this->request);

        $this->eventManager->expects($this->at(0))->method('trigger')->with('authenticate.pre');

        /**
         * @var $response \Laminas\EventManager\ResponseCollection
         */
        $responses = $this->getMockBuilder(ResponseCollection::class)
            ->getMock();

        $this->eventManager->expects($this->at(1))
            ->method('trigger')
            ->with('authenticate', $this->event)
            ->will($this->returnCallback(function ($event, $target, $callback) use ($responses) {
                if (call_user_func($callback, $responses->last())) {
                    $responses->setStopped(true);
                }
                return $responses;
            }));

        $this->adapterChain->setEvent($this->event);

        return $responses;
    }

    /**
     * Provider for testPrepareForAuthentication()
     *
     * @return array
     */
    public function identityProvider(): array
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    /**
     * Tests prepareForAuthentication when falls through events.
     *
     * @param mixed $identity
     * @param bool  $expected
     *
     * @dataProvider identityProvider
     * @covers \ZfcUser\Authentication\Adapter\AdapterChain::prepareForAuthentication
     */
    public function testPrepareForAuthentication($identity, $expected): void
    {
        $result = $this->setUpPrepareForAuthentication();

        $result->expects($this->once())->method('stopped')->will($this->returnValue(false));

        $this->event->expects($this->once())->method('getIdentity')->will($this->returnValue($identity));

        $this->assertEquals(
            $expected,
            $this->adapterChain->prepareForAuthentication($this->request),
            'Asserting prepareForAuthentication() returns true'
        );
    }

    /**
     * Test prepareForAuthentication() when the returned collection contains stopped.
     *
     * @covers \ZfcUser\Authentication\Adapter\AdapterChain::prepareForAuthentication
     */
    public function testPrepareForAuthenticationWithStoppedEvent(): void
    {
        $result = $this->setUpPrepareForAuthentication();

        $result->expects($this->once())->method('stopped')->will($this->returnValue(true));

        $lastResponse = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $result->expects($this->atLeastOnce())->method('last')->will($this->returnValue($lastResponse));

        $this->assertEquals(
            $lastResponse,
            $this->adapterChain->prepareForAuthentication($this->request),
            'Asserting the Response returned from the event is returned'
        );
    }

    /**
     * Test prepareForAuthentication() when the returned collection contains stopped.
     *
     * @covers \ZfcUser\Authentication\Adapter\AdapterChain::prepareForAuthentication
     */
    public function testPrepareForAuthenticationWithBadEventResult(): void
    {
        $this->expectException(AuthenticationEventException::class);

        $result = $this->setUpPrepareForAuthentication();

        $result->expects($this->once())->method('stopped')->will($this->returnValue(true));

        $lastResponse = 'random-value';
        $result->expects($this->atLeastOnce())->method('last')->will($this->returnValue($lastResponse));

        $this->adapterChain->prepareForAuthentication($this->request);
    }

    /**
     * Test getEvent() when no event has previously been set.
     *
     * @covers \ZfcUser\Authentication\Adapter\AdapterChain::getEvent
     */
    public function testGetEventWithNoEventSet(): void
    {
        $event = $this->adapterChain->getEvent();

        $this->assertInstanceOf(
            AdapterChainEvent::class,
            $event,
            'Asserting the adapter in an instance of ZfcUser\Authentication\Adapter\AdapterChainEvent'
        );
        $this->assertEquals(
            $this->adapterChain,
            $event->getTarget(),
            'Asserting the Event target is the AdapterChain'
        );
    }

    /**
     * Test getEvent() when an event has previously been set.
     *
     * @covers \ZfcUser\Authentication\Adapter\AdapterChain::setEvent
     * @covers \ZfcUser\Authentication\Adapter\AdapterChain::getEvent
     */
    public function testGetEventWithEventSet(): void
    {
        $event = new AdapterChainEvent();

        $this->adapterChain->setEvent($event);

        $this->assertEquals(
            $event,
            $this->adapterChain->getEvent(),
            'Asserting the event fetched is the same as the event set'
        );
    }

    /**
     * Tests the mechanism for casting one event type to AdapterChainEvent
     *
     * @covers \ZfcUser\Authentication\Adapter\AdapterChain::setEvent
     */
    public function testSetEventWithDifferentEventType(): void
    {
        $testParams = ['testParam' => 'testValue'];

        $event = new Event();
        $event->setParams($testParams);

        $this->adapterChain->setEvent($event);
        $returnEvent = $this->adapterChain->getEvent();

        $this->assertInstanceOf(
            AdapterChainEvent::class,
            $returnEvent,
            'Asserting the adapter in an instance of ZfcUser\Authentication\Adapter\AdapterChainEvent'
        );

        $this->assertEquals(
            $testParams,
            $returnEvent->getParams(),
            'Asserting event parameters match'
        );
    }

    /**
     * Test the logoutAdapters method.
     *
     * @depends testGetEventWithEventSet
     * @covers \ZfcUser\Authentication\Adapter\AdapterChain::logoutAdapters
     */
    public function testLogoutAdapters(): void
    {
        $event = new AdapterChainEvent();

        $this->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with('logout', $event);

        $this->adapterChain->setEvent($event);
        $this->adapterChain->logoutAdapters();
    }
}
