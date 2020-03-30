<?php

namespace UserAuthenticatorTest\Authentication\Storage;

use Laminas\Authentication\Storage\Session;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Authentication\Storage\Db;
use UserAuthenticator\Mapper\User as UserMapper;
use UserAuthenticator\Mapper\UserInterface;
use UserAuthenticator\Model\User;
use ReflectionClass;

class DbTest extends TestCase
{
    /**
     * The object to be tested.
     *
     * @var Db
     */
    protected $db;

    /**
     * Mock of Storage.
     *
     * @var Session
     */
    protected $storage;

    /**
     * @var UserMapper
     */
    protected $mapper;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->db = new Db();

        $this->storage = $this->getMockBuilder(Session::class)
            ->getMock();
        $this->mapper = $this->getMockBuilder(UserMapper::class)
            ->getMock();
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->db);
        unset($this->storage);
        unset($this->mapper);
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::isEmpty
     */
    public function testIsEmpty(): void
    {
        $this->storage->expects($this->once())
            ->method('isEmpty')
            ->will($this->returnValue(true));

        $this->db->setStorage($this->storage);

        $this->assertTrue($this->db->isEmpty());
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::read
     */
    public function testReadWithResolvedEntitySet(): void
    {
        $reflectionClass = new ReflectionClass(Db::class);
        $reflectionProperty = $reflectionClass->getProperty('resolvedIdentity');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->db, 'zfcUser');

        $this->assertSame('zfcUser', $this->db->read());
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::read
     */
    public function testReadWithoutResolvedEntitySetIdentityIntUserFound(): void
    {
        $this->storage->expects($this->once())
            ->method('read')
            ->will($this->returnValue(1));

        $this->db->setStorage($this->storage);

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->setUsername('zfcUser');

        $this->mapper->expects($this->once())
            ->method('findById')
            ->with(1)
            ->will($this->returnValue($user));

        $this->db->setMapper($this->mapper);

        $result = $this->db->read();

        $this->assertSame($user, $result);
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::read
     */
    public function testReadWithoutResolvedEntitySetIdentityIntUserNotFound(): void
    {
        $this->storage->expects($this->once())
            ->method('read')
            ->will($this->returnValue(1));

        $this->db->setStorage($this->storage);

        $this->mapper->expects($this->once())
            ->method('findById')
            ->with(1)
            ->will($this->returnValue(false));

        $this->db->setMapper($this->mapper);

        $result = $this->db->read();

        $this->assertNull($result);
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::read
     */
    public function testReadWithoutResolvedEntitySetIdentityObject(): void
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->setUsername('zfcUser');

        $this->storage->expects($this->once())
            ->method('read')
            ->will($this->returnValue($user));

        $this->db->setStorage($this->storage);

        $result = $this->db->read();

        $this->assertSame($user, $result);
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::write
     */
    public function testWrite(): void
    {
        $reflectionClass = new ReflectionClass(Db::class);
        $reflectionProperty = $reflectionClass->getProperty('resolvedIdentity');
        $reflectionProperty->setAccessible(true);

        $this->storage->expects($this->once())
            ->method('write')
            ->with('zfcUser');

        $this->db->setStorage($this->storage);

        $this->db->write('zfcUser');

        $this->assertNull($reflectionProperty->getValue($this->db));
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::clear
     */
    public function testClear(): void
    {
        $reflectionClass = new ReflectionClass(Db::class);
        $reflectionProperty = $reflectionClass->getProperty('resolvedIdentity');
        $reflectionProperty->setAccessible(true);

        $this->storage->expects($this->once())
            ->method('clear');

        $this->db->setStorage($this->storage);

        $this->db->clear();

        $this->assertNull($reflectionProperty->getValue($this->db));
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::getMapper
     */
    public function testGetMapperWithNoMapperSet(): void
    {
        $sm = $this->getMockBuilder(ServiceManager::class)
            ->getMock();
        $sm->expects($this->once())
            ->method('get')
            ->with('zfcuser_user_mapper')
            ->will($this->returnValue($this->mapper));

        $this->db->setServiceManager($sm);

        $this->assertInstanceOf(UserInterface::class, $this->db->getMapper());
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::setMapper
     * @covers UserAuthenticator\Authentication\Storage\Db::getMapper
     */
    public function testSetGetMapper(): void
    {
        $mapper = new UserMapper();
        $mapper->setTableName('zfcUser');

        $this->db->setMapper($mapper);

        $this->assertInstanceOf(UserMapper::class, $this->db->getMapper());
        $this->assertSame('zfcUser', $this->db->getMapper()->getTableName());
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::setServiceManager
     * @covers UserAuthenticator\Authentication\Storage\Db::getServiceManager
     */
    public function testSetGetServicemanager(): void
    {
        $sm = $this->getMockBuilder(ServiceManager::class)
            ->getMock();

        $this->db->setServiceManager($sm);

        $this->assertInstanceOf(ServiceLocatorInterface::class, $this->db->getServiceManager());
        $this->assertSame($sm, $this->db->getServiceManager());
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::getStorage
     * @covers UserAuthenticator\Authentication\Storage\Db::setStorage
     */
    public function testGetStorageWithoutStorageSet(): void
    {
        $this->assertInstanceOf(Session::class, $this->db->getStorage());
    }

    /**
     * @covers UserAuthenticator\Authentication\Storage\Db::getStorage
     * @covers UserAuthenticator\Authentication\Storage\Db::setStorage
     */
    public function testSetGetStorage(): void
    {
        $storage = new Session('ZfcUserStorage');
        $this->db->setStorage($storage);

        $this->assertInstanceOf(Session::class, $this->db->getStorage());
    }
}
