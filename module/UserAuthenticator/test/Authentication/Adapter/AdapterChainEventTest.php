<?php

namespace UserAuthenticatorTest\Authentication\Adapter;

use Laminas\Stdlib\RequestInterface;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Authentication\Adapter\AdapterChainEvent;

class AdapterChainEventTest extends TestCase
{
    /**
     * The object to be tested.
     *
     * @var AdapterChainEvent
     */
    protected $event;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->event = new AdapterChainEvent();
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->event);
    }

    /**
     * @covers \UserAuthenticator\Authentication\Adapter\AdapterChainEvent::getCode
     * @covers \UserAuthenticator\Authentication\Adapter\AdapterChainEvent::setCode
     * @covers \UserAuthenticator\Authentication\Adapter\AdapterChainEvent::getMessages
     * @covers \UserAuthenticator\Authentication\Adapter\AdapterChainEvent::setMessages
     */
    public function testCodeAndMessages(): void
    {
        $testCode = 103;
        $testMessages = ['Message recieved loud and clear.'];

        $this->event->setCode($testCode);
        $this->assertEquals($testCode, $this->event->getCode(), 'Asserting code values match.');

        $this->event->setMessages($testMessages);
        $this->assertEquals($testMessages, $this->event->getMessages(), 'Asserting messages values match.');
    }

    /**
     * @depends testCodeAndMessages
     * @covers \UserAuthenticator\Authentication\Adapter\AdapterChainEvent::getIdentity
     * @covers \UserAuthenticator\Authentication\Adapter\AdapterChainEvent::setIdentity
     */
    public function testIdentity(): void
    {
        $testCode = 123;
        $testMessages = ['The message.'];
        $testIdentity = 'the_user';

        $this->event->setCode($testCode);
        $this->event->setMessages($testMessages);

        $this->event->setIdentity($testIdentity);

        $this->assertEquals($testCode, $this->event->getCode(), 'Asserting the code persisted.');
        $this->assertEquals($testMessages, $this->event->getMessages(), 'Asserting the messages persisted.');
        $this->assertEquals($testIdentity, $this->event->getIdentity(), 'Asserting the identity matches');

        $this->event->setIdentity();

        $this->assertNull($this->event->getCode(), 'Asserting the code has been cleared.');
        $this->assertEquals([], $this->event->getMessages(), 'Asserting the messages have been cleared.');
        $this->assertNull($this->event->getIdentity(), 'Asserting the identity has been cleared');
    }

    public function testRequest(): void
    {
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $this->event->setRequest($request);

        $this->assertInstanceOf(RequestInterface::class, $this->event->getRequest());
    }
}
