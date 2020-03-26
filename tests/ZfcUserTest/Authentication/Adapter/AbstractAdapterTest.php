<?php

namespace ZfcUserTest\Authentication\Adapter;

use Laminas\Authentication\Storage\Session;
use PHPUnit\Framework\TestCase;
use ZfcUserTest\Authentication\Adapter\TestAsset\AbstractAdapterExtension;
use ZfcUser\Authentication\Adapter\AbstractAdapter;

class AbstractAdapterTest extends TestCase
{
    /**
     * The object to be tested.
     *
     * @var AbstractAdapterExtension
     */
    protected $adapter;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->adapter = new AbstractAdapterExtension();
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->adapter);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\AbstractAdapter::getStorage
     */
    public function testGetStorageWithoutStorageSet(): void
    {
        $this->assertInstanceOf(Session::class, $this->adapter->getStorage());
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\AbstractAdapter::getStorage
     * @covers \ZfcUser\Authentication\Adapter\AbstractAdapter::setStorage
     */
    public function testSetGetStorage(): void
    {
        $storage = new Session('ZfcUser');
        $storage->write('zfcUser');
        $this->adapter->setStorage($storage);

        $this->assertInstanceOf(Session::class, $this->adapter->getStorage());
        $this->assertSame('zfcUser', $this->adapter->getStorage()->read());
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\AbstractAdapter::isSatisfied
     */
    public function testIsSatisfied(): void
    {
        $this->assertFalse($this->adapter->isSatisfied());
    }

    public function testSetSatisfied(): void
    {
        $result = $this->adapter->setSatisfied();
        $this->assertInstanceOf(AbstractAdapter::class, $result);
        $this->assertTrue($this->adapter->isSatisfied());

        $result = $this->adapter->setSatisfied(false);
        $this->assertInstanceOf(AbstractAdapter::class, $result);
        $this->assertFalse($this->adapter->isSatisfied());
    }
}
