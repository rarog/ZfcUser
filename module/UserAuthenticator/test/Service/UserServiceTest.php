<?php

namespace UserAuthenticatorTest\Service;

use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\EventManager\EventManager;
use Laminas\Hydrator\HydratorInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use UserAuthenticator\Mapper\User as UserMapper;
use UserAuthenticator\Model\User;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Service\UserService;
use ReflectionClass;

class UserServiceTest extends TestCase
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var UserMapper|MockObject
     */
    protected $userMapper;

    /**
     * @var AuthenticationService|MockObject
     */
    protected $authService;

    /**
     * @var ModuleOptions|MockObject
     */
    protected $moduleOptions;

    /**
     * @var EventManager|MockObject
     */
    protected $eventManager;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $formHydrator = $this->getMockBuilder(HydratorInterface::class)
            ->getMock();
        $this->formHydrator = $formHydrator;

        $userMapper = $this->getMockBuilder(UserMapper::class)
            ->getMock();
        $this->userMapper = $userMapper;

        $authService = $this->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->authService = $authService;

        $eventManager = $this->getMockBuilder(EventManager::class)
            ->getMock();
        $this->eventManager = $eventManager;

        $moduleOptions = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $this->moduleOptions = $moduleOptions;

        $userService = new UserService(
            $userMapper,
            $authService,
            $moduleOptions
        );
        $userService->setEventManager($eventManager);

        $this->userService = $userService;
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->eventManager);
        unset($this->moduleOptions);
        unset($this->authService);
        unset($this->userMapper);
        unset($this->userService);
    }

    /**
     * @covers UserAuthenticator\Service\UserService::__construct
     */
    public function testConstructor(): void
    {
        $reflection = new ReflectionClass(UserService::class);

        $property = $reflection->getProperty('userMapper');
        $property->setAccessible(true);
        $this->assertSame(
            $this->userMapper,
            $property->getValue($this->userService)
        );

        $property = $reflection->getProperty('authService');
        $property->setAccessible(true);
        $this->assertSame(
            $this->authService,
            $property->getValue($this->userService)
        );

        $property = $reflection->getProperty('moduleOptions');
        $property->setAccessible(true);
        $this->assertSame(
            $this->moduleOptions,
            $property->getValue($this->userService)
        );
    }

    /**
     * @covers UserAuthenticator\Service\UserService::register
     */
    public function testRegisterWithUsernameAndDisplayNameUserStateDisabled(): void
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('setPassword');
        $user->expects($this->once())
            ->method('getPassword');
        $user->expects($this->once())
            ->method('setState')
            ->with(1);

        $this->moduleOptions->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));
        $this->moduleOptions->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(true));
        $this->moduleOptions->expects($this->once())
            ->method('getDefaultUserState')
            ->will($this->returnValue(1));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->userMapper->expects($this->once())
            ->method('insert')
            ->with($user)
            ->will($this->returnValue($user));

        $result = $this->userService->register($user);

        $this->assertSame($user, $result);
    }

    /**
     * @covers UserAuthenticator\Service\UserService::register
     */
    public function testRegisterWithDefaultUserStateOfZero(): void
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('setPassword');
        $user->expects($this->once())
            ->method('getPassword');
        $user->expects($this->once())
            ->method('setState')
            ->with(0);

        $this->moduleOptions->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));
        $this->moduleOptions->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(true));
        $this->moduleOptions->expects($this->once())
            ->method('getDefaultUserState')
            ->will($this->returnValue(0));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->userMapper->expects($this->once())
            ->method('insert')
            ->with($user)
            ->will($this->returnValue($user));

        $result = $this->userService->register($user);

        $this->assertSame($user, $result);
        $this->assertEquals(0, $user->getState());
    }

    /**
     * @covers UserAuthenticator\Service\UserService::register
     */
    public function testRegisterWithUserStateDisabled(): void
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('setPassword');
        $user->expects($this->once())
            ->method('getPassword');
        $user->expects($this->never())
            ->method('setState');

        $this->moduleOptions->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));
        $this->moduleOptions->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(false));
        $this->moduleOptions->expects($this->never())
            ->method('getDefaultUserState');

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->userMapper->expects($this->once())
            ->method('insert')
            ->with($user)
            ->will($this->returnValue($user));

        $result = $this->userService->register($user);

        $this->assertSame($user, $result);
        $this->assertEquals(0, $user->getState());
    }

    /**
     * @covers UserAuthenticator\Service\UserService::changePassword
     */
    public function testChangePasswordWithWrongOldPassword(): void
    {
        $data = ['newCredential' => 'zfcUser', 'credential' => 'zfcUserOld'];

        $this->moduleOptions->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->moduleOptions->getPasswordCost());

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($bcrypt->create('wrongPassword')));

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $result = $this->userService->changePassword($data);
        $this->assertFalse($result);
    }

    /**
     * @covers UserAuthenticator\Service\UserService::changePassword
     */
    public function testChangePassword(): void
    {
        $data = ['newCredential' => 'zfcUser', 'credential' => 'zfcUserOld'];

        $this->moduleOptions->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->moduleOptions->getPasswordCost());

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($bcrypt->create($data['credential'])));
        $user->expects($this->any())
            ->method('setPassword');

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->userMapper->expects($this->once())
            ->method('update')
            ->with($user);

        $result = $this->userService->changePassword($data);
        $this->assertTrue($result);
    }

    /**
     * @covers UserAuthenticator\Service\UserService::changeEmail
     */
    public function testChangeEmail(): void
    {
        $data = ['credential' => 'zfcUser', 'newIdentity' => 'zfcUser@zfcUser.com'];

        $this->moduleOptions->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->moduleOptions->getPasswordCost());

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($bcrypt->create($data['credential'])));
        $user->expects($this->any())
            ->method('setEmail')
            ->with('zfcUser@zfcUser.com');

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->userMapper->expects($this->once())
            ->method('update')
            ->with($user);

        $result = $this->userService->changeEmail($data);
        $this->assertTrue($result);
    }

    /**
     * @covers UserAuthenticator\Service\UserService::changeEmail
     */
    public function testChangeEmailWithWrongPassword(): void
    {
        $data = ['credential' => 'zfcUserOld'];

        $this->moduleOptions->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->moduleOptions->getPasswordCost());

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($bcrypt->create('wrongPassword')));

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $result = $this->userService->changeEmail($data);
        $this->assertFalse($result);
    }
}
