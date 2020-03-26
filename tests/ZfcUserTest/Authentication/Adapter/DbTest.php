<?php

namespace ZfcUserTest\Authentication\Adapter;

use Laminas\Authentication\Result;
use Laminas\Authentication\Storage\Session;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\EventManager\Event;
use Laminas\Http\Request;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Session\AbstractContainer;
use Laminas\Session\SessionManager;
use Laminas\Stdlib\Parameters;
use PHPUnit\Framework\TestCase;
use ZfcUser\Authentication\Adapter\AdapterChainEvent;
use ZfcUser\Authentication\Adapter\Db;
use ZfcUser\Entity\User;
use ZfcUser\Entity\UserInterface;
use ZfcUser\Mapper\User as UserMapper;
use ZfcUser\Mapper\UserInterface as UserInterfaceMapper;
use ZfcUser\Options\ModuleOptions;
use ReflectionMethod;

class DbTest extends TestCase
{
    /**
     * The object to be tested.
     *
     * @var Db
     */
    protected $db;

    /**
     * Mock of AuthEvent.
     *
     * @var \ZfcUser\Authentication\Adapter\AdapterChainEvent|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $authEvent;

    /**
     * Mock of Storage.
     *
     * @var \Laminas\Authentication\Storage\Session|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $storage;

    /**
     * Mock of Options.
     *
     * @var \ZfcUser\Options\ModuleOptions|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $options;

    /**
     * Mock of Mapper.
     *
     * @var \ZfcUser\Mapper\UserInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mapper;

    /**
     * Mock of User.
     *
     * @var \ZfcUser\Entity\UserInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $user;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->storage = $this->getMockBuilder(Session::class)
            ->getMock();

        $this->authEvent = $this->getMockBuilder(AdapterChainEvent::class)
            ->getMock();

        $this->options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();

        $this->mapper = $this->getMockBuilder(UserInterfaceMapper::class)
            ->getMock();

        $this->user = $this->getMockBuilder(UserInterface::class)
            ->getMock();

        $this->db = new Db();
        $this->db->setStorage($this->storage);

        $sessionManager = $this->getMockBuilder(SessionManager::class)
            ->getMock();
        AbstractContainer::setDefaultManager($sessionManager);
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->db);
        unset($this->user);
        unset($this->mapper);
        unset($this->options);
        unset($this->authEvent);
        unset($this->storage);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::logout
     */
    public function testLogout(): void
    {
        $this->storage->expects($this->once())
            ->method('clear');

         $this->db->logout($this->authEvent);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateWhenSatisfies(): void
    {
        /*$this->authEvent->expects($this->once())
            ->method('setIdentity')
            ->with('ZfcUser')
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(Result::SUCCESS)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(['Authentication successful.'])
            ->will($this->returnValue($this->authEvent));*/

        $this->storage->expects($this->at(0))
            ->method('read')
            ->will($this->returnValue(['is_satisfied' => true]));
        $this->storage->expects($this->at(1))
            ->method('read')
            ->will($this->returnValue(['identity' => 'ZfcUser']));

        $event = new AdapterChainEvent(null, $this->authEvent);

        $result = $this->db->authenticate($event);
        $this->assertNull($result);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateNoUserObject(): void
    {
        $this->setAuthenticationCredentials();

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue([]));

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(Result::FAILURE_IDENTITY_NOT_FOUND)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(['A record with the supplied identity could not be found.'])
            ->will($this->returnValue($this->authEvent));

        $this->db->setOptions($this->options);

        $event = new AdapterChainEvent(null, $this->authEvent);
        $result = $this->db->authenticate($event);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationUserStateEnabledUserButUserStateNotInArray(): void
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getAllowedLoginStates')
            ->will($this->returnValue([2, 3]));

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(Result::FAILURE_UNCATEGORIZED)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(['A record with the supplied identity is not active.'])
            ->will($this->returnValue($this->authEvent));

        $this->user->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(1));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $event = new AdapterChainEvent(null, $this->authEvent);
        $result = $this->db->authenticate($event);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateWithWrongPassword(): void
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(false));

        // Set lowest possible to spent the least amount of resources/time
        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(Result::FAILURE_CREDENTIAL_INVALID)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once(1))
            ->method('setMessages')
            ->with(['Supplied credential is invalid.']);

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $event = new AdapterChainEvent(null, $this->authEvent);
        $result = $this->db->authenticate($event);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationAuthenticatesWithEmail(): void
    {
        $this->setAuthenticationCredentials('zfc-user@zf-commons.io');
        $this->setAuthenticationEmail();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(false));

        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $this->user->expects($this->exactly(2))
            ->method('getPassword')
            ->will($this->returnValue('$2a$04$5kq1mnYWbww8X.rIj7eOVOHXtvGw/peefjIcm0lDGxRTEjm9LnOae'));
        $this->user->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->storage->expects($this->any())
            ->method('getNameSpace')
            ->will($this->returnValue('test'));

        $this->authEvent->expects($this->once())
            ->method('setIdentity')
            ->with(1)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(Result::SUCCESS)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(['Authentication successful.'])
            ->will($this->returnValue($this->authEvent));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $event = new AdapterChainEvent(null, $this->authEvent);
        $this->db->authenticate($event);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationAuthenticates(): void
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(true));

        $this->options->expects($this->once())
            ->method('getAllowedLoginStates')
            ->will($this->returnValue([1, 2, 3]));

        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $this->user->expects($this->exactly(2))
            ->method('getPassword')
            ->will($this->returnValue('$2a$04$5kq1mnYWbww8X.rIj7eOVOHXtvGw/peefjIcm0lDGxRTEjm9LnOae'));
        $this->user->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->user->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(1));

        $this->storage->expects($this->any())
            ->method('getNameSpace')
            ->will($this->returnValue('test'));

        $this->authEvent->expects($this->once())
            ->method('setIdentity')
            ->with(1)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(Result::SUCCESS)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(['Authentication successful.'])
            ->will($this->returnValue($this->authEvent));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $event = new AdapterChainEvent(null, $this->authEvent);
        $this->db->authenticate($event);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::updateUserPasswordHash
     */
    public function testUpdateUserPasswordHashWithSameCost(): void
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('getPassword')
            ->will($this->returnValue('$2a$10$x05G2P803MrB3jaORBXBn.QHtiYzGQOBjQ7unpEIge.Mrz6c3KiVm'));

        $bcrypt = $this->getMockBuilder(Bcrypt::class)
            ->getMock();
        $bcrypt->expects($this->once())
            ->method('getCost')
            ->will($this->returnValue('10'));

        $method = new ReflectionMethod(
            Db::class,
            'updateUserPasswordHash'
        );
        $method->setAccessible(true);

        $result = $method->invoke($this->db, $user, 'ZfcUser', $bcrypt);
        $this->assertNull($result);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::updateUserPasswordHash
     */
    public function testUpdateUserPasswordHashWithoutSameCost(): void
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('getPassword')
            ->will($this->returnValue('$2a$10$x05G2P803MrB3jaORBXBn.QHtiYzGQOBjQ7unpEIge.Mrz6c3KiVm'));
        $user->expects($this->once())
            ->method('setPassword')
            ->with('$2a$10$D41KPuDCn6iGoESjnLee/uE/2Xo985sotVySo2HKDz6gAO4hO/Gh6');

        $bcrypt = $this->getMockBuilder(Bcrypt::class)
            ->getMock();
        $bcrypt->expects($this->once())
            ->method('getCost')
            ->will($this->returnValue('5'));
        $bcrypt->expects($this->once())
            ->method('create')
            ->with('ZfcUserNew')
            ->will($this->returnValue('$2a$10$D41KPuDCn6iGoESjnLee/uE/2Xo985sotVySo2HKDz6gAO4hO/Gh6'));

        $mapper = $this->getMockBuilder(UserMapper::class)
            ->getMock();
        $mapper->expects($this->once())
            ->method('update')
            ->with($user);

        $this->db->setMapper($mapper);

        $method = new ReflectionMethod(
            Db::class,
            'updateUserPasswordHash'
        );
        $method->setAccessible(true);

        $result = $method->invoke($this->db, $user, 'ZfcUserNew', $bcrypt);
        $this->assertInstanceOf(Db::class, $result);
    }


    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::preprocessCredential
     * @covers \ZfcUser\Authentication\Adapter\Db::setCredentialPreprocessor
     * @covers \ZfcUser\Authentication\Adapter\Db::getCredentialPreprocessor
     */
    public function testPreprocessCredentialWithCallable(): void
    {
        $test = $this;
        $testVar = false;
        $callable = function ($credential) use ($test, &$testVar) {
            $test->assertEquals('ZfcUser', $credential);
            $testVar = true;
        };
        $this->db->setCredentialPreprocessor($callable);

        $this->db->preProcessCredential('ZfcUser');
        $this->assertTrue($testVar);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::preprocessCredential
     * @covers \ZfcUser\Authentication\Adapter\Db::setCredentialPreprocessor
     * @covers \ZfcUser\Authentication\Adapter\Db::getCredentialPreprocessor
     */
    public function testPreprocessCredentialWithoutCallable(): void
    {
        $this->db->setCredentialPreprocessor(false);
        $this->assertSame('zfcUser', $this->db->preProcessCredential('zfcUser'));
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::setServiceManager
     * @covers \ZfcUser\Authentication\Adapter\Db::getServiceManager
     */
    public function testSetGetServicemanager(): void
    {
        $sm = $this->getMockBuilder(ServiceManager::class)
            ->getMock();

        $this->db->setServiceManager($sm);

        $serviceManager = $this->db->getServiceManager();

        $this->assertInstanceOf(ServiceLocatorInterface::class, $serviceManager);
        $this->assertSame($sm, $serviceManager);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::getOptions
     */
    public function testGetOptionsWithNoOptionsSet(): void
    {
        $serviceMapper = $this->getMockBuilder(ServiceManager::class)
            ->getMock();
        $serviceMapper->expects($this->once())
            ->method('get')
            ->with('zfcuser_module_options')
            ->will($this->returnValue($this->options));

        $this->db->setServiceManager($serviceMapper);

        $options = $this->db->getOptions();

        $this->assertInstanceOf(ModuleOptions::class, $options);
        $this->assertSame($this->options, $options);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::setOptions
     * @covers \ZfcUser\Authentication\Adapter\Db::getOptions
     */
    public function testSetGetOptions(): void
    {
        $options = new ModuleOptions();
        $options->setLoginRedirectRoute('zfcUser');

        $this->db->setOptions($options);

        $this->assertInstanceOf(ModuleOptions::class, $this->db->getOptions());
        $this->assertSame('zfcUser', $this->db->getOptions()->getLoginRedirectRoute());
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::getMapper
     */
    public function testGetMapperWithNoMapperSet(): void
    {
        $serviceMapper = $this->getMockBuilder(ServiceManager::class)
            ->getMock();
        $serviceMapper->expects($this->once())
            ->method('get')
            ->with('zfcuser_user_mapper')
            ->will($this->returnValue($this->mapper));

        $this->db->setServiceManager($serviceMapper);

        $mapper = $this->db->getMapper();
        $this->assertInstanceOf(UserInterfaceMapper::class, $mapper);
        $this->assertSame($this->mapper, $mapper);
    }

    /**
     * @covers \ZfcUser\Authentication\Adapter\Db::setMapper
     * @covers \ZfcUser\Authentication\Adapter\Db::getMapper
     */
    public function testSetGetMapper(): void
    {
        $mapper = new UserMapper();
        $mapper->setTableName('zfcUser');

        $this->db->setMapper($mapper);

        $this->assertInstanceOf(UserMapper::class, $this->db->getMapper());
        $this->assertSame('zfcUser', $this->db->getMapper()->getTableName());
    }

    protected function setAuthenticationEmail(): void
    {
        $this->mapper->expects($this->once())
            ->method('findByEmail')
            ->with('zfc-user@zf-commons.io')
            ->will($this->returnValue($this->user));

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(['email']));
    }

    protected function setAuthenticationUser(): void
    {
        $this->mapper->expects($this->once())
            ->method('findByUsername')
            ->with('ZfcUser')
            ->will($this->returnValue($this->user));

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(['username']));
    }

    protected function setAuthenticationCredentials($identity = 'ZfcUser', $credential = 'ZfcUserPassword'): void
    {
        $this->storage->expects($this->at(0))
            ->method('read')
            ->will($this->returnValue(['is_satisfied' => false]));

        $post = $this->getMockBuilder(Parameters::class)
            ->getMock();
        $post->expects($this->at(0))
            ->method('get')
            ->with('identity')
            ->will($this->returnValue($identity));
        $post->expects($this->at(1))
            ->method('get')
            ->with('credential')
            ->will($this->returnValue($credential));

        $request = $this->getMockBuilder(Request::class)
            ->getMock();
        $request->expects($this->exactly(2))
            ->method('getPost')
            ->will($this->returnValue($post));

        $this->authEvent->expects($this->exactly(2))
            ->method('getRequest')
            ->will($this->returnValue($request));
    }
}
