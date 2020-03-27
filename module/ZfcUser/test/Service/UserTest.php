<?php

namespace ZfcUserTest\Service;

use Laminas\Crypt\Password\Bcrypt;
use PHPUnit\Framework\TestCase;
use ZfcUser\Entity\User;
use ZfcUser\Form\Register;
use ZfcUser\Mapper\UserInterface;
use ZfcUser\Options\ModuleOptions;
use ZfcUser\Service\User as Service;
use ZfcUser\Form\ChangePassword;
use Laminas\ServiceManager\ServiceManager;
use Laminas\EventManager\EventManager;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Authentication\AuthenticationService;

class UserTest extends TestCase
{
    protected $service;

    protected $options;

    protected $serviceManager;

    protected $formHydrator;

    protected $eventManager;

    protected $mapper;

    protected $authService;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $service = new Service();
        $this->service = $service;

        $options = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        $this->options = $options;

        $serviceManager = $this->getMockBuilder(ServiceManager::class)
            ->getMock();
        $this->serviceManager = $serviceManager;

        $eventManager = $this->getMockBuilder(EventManager::class)
            ->getMock();
        $this->eventManager = $eventManager;

        $formHydrator = $this->getMockBuilder(HydratorInterface::class)
            ->getMock();
        $this->formHydrator = $formHydrator;

        $mapper = $this->getMockBuilder(UserInterface::class)
            ->getMock();
        $this->mapper = $mapper;

        $authService = $this->getMockBuilder(AuthenticationService::class)->disableOriginalConstructor()->getMock();
        $this->authService = $authService;

        $service->setOptions($options);
        $service->setServiceManager($serviceManager);
        $service->setFormHydrator($formHydrator);
        $service->setEventManager($eventManager);
        $service->setUserMapper($mapper);
        $service->setAuthService($authService);
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->authService);
        unset($this->mapper);
        unset($this->formHydrator);
        unset($this->eventManager);
        unset($this->serviceManager);
        unset($this->options);
        unset($this->service);
    }

    /**
     * @covers ZfcUser\Service\User::register
     */
    public function testRegisterWithInvalidForm(): void
    {
        $expectArray = ['username' => 'ZfcUser'];

        $this->options->expects($this->once())
            ->method('getUserEntityClass')
            ->will($this->returnValue(User::class));

        $registerForm = $this->getMockBuilder(Register::class)->disableOriginalConstructor()->getMock();
        $registerForm->expects($this->once())
            ->method('setHydrator');
        $registerForm->expects($this->once())
            ->method('bind');
        $registerForm->expects($this->once())
            ->method('setData')
            ->with($expectArray);
        $registerForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->service->setRegisterForm($registerForm);

        $result = $this->service->register($expectArray);

        $this->assertFalse($result);
    }

    /**
     * @covers ZfcUser\Service\User::register
     */
    public function testRegisterWithUsernameAndDisplayNameUserStateDisabled(): void
    {
        $expectArray = ['username' => 'ZfcUser', 'display_name' => 'Zfc User'];

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('setPassword');
        $user->expects($this->once())
            ->method('getPassword');
        $user->expects($this->once())
            ->method('setUsername')
            ->with('ZfcUser');
        $user->expects($this->once())
            ->method('setDisplayName')
            ->with('Zfc User');
        $user->expects($this->once())
            ->method('setState')
            ->with(1);

        $this->options->expects($this->once())
            ->method('getUserEntityClass')
            ->will($this->returnValue(User::class));
        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));
        $this->options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getDefaultUserState')
            ->will($this->returnValue(1));

        $registerForm = $this->getMockBuilder(Register::class)->disableOriginalConstructor()->getMock();
        $registerForm->expects($this->once())
            ->method('setHydrator');
        $registerForm->expects($this->once())
            ->method('bind');
        $registerForm->expects($this->once())
            ->method('setData')
            ->with($expectArray);
        $registerForm->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($user));
        $registerForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->mapper->expects($this->once())
            ->method('insert')
            ->with($user)
            ->will($this->returnValue($user));

        $this->service->setRegisterForm($registerForm);

        $result = $this->service->register($expectArray);

        $this->assertSame($user, $result);
    }

    /**
     * @covers ZfcUser\Service\User::register
     */
    public function testRegisterWithDefaultUserStateOfZero(): void
    {
        $expectArray = ['username' => 'ZfcUser', 'display_name' => 'Zfc User'];

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('setPassword');
        $user->expects($this->once())
            ->method('getPassword');
        $user->expects($this->once())
            ->method('setUsername')
            ->with('ZfcUser');
        $user->expects($this->once())
            ->method('setDisplayName')
            ->with('Zfc User');
        $user->expects($this->once())
            ->method('setState')
            ->with(0);

        $this->options->expects($this->once())
            ->method('getUserEntityClass')
            ->will($this->returnValue(User::class));
        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));
        $this->options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getDefaultUserState')
            ->will($this->returnValue(0));

        $registerForm = $this->getMockBuilder(Register::class)->disableOriginalConstructor()->getMock();
        $registerForm->expects($this->once())
            ->method('setHydrator');
        $registerForm->expects($this->once())
            ->method('bind');
        $registerForm->expects($this->once())
            ->method('setData')
            ->with($expectArray);
        $registerForm->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($user));
        $registerForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->mapper->expects($this->once())
            ->method('insert')
            ->with($user)
            ->will($this->returnValue($user));

        $this->service->setRegisterForm($registerForm);

        $result = $this->service->register($expectArray);

        $this->assertSame($user, $result);
        $this->assertEquals(0, $user->getState());
    }

    /**
     * @covers ZfcUser\Service\User::register
     */
    public function testRegisterWithUserStateDisabled(): void
    {
        $expectArray = ['username' => 'ZfcUser', 'display_name' => 'Zfc User'];

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('setPassword');
        $user->expects($this->once())
            ->method('getPassword');
        $user->expects($this->once())
            ->method('setUsername')
            ->with('ZfcUser');
        $user->expects($this->once())
            ->method('setDisplayName')
            ->with('Zfc User');
        $user->expects($this->never())
            ->method('setState');

        $this->options->expects($this->once())
            ->method('getUserEntityClass')
            ->will($this->returnValue(User::class));
        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));
        $this->options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(false));
        $this->options->expects($this->never())
            ->method('getDefaultUserState');

        $registerForm = $this->getMockBuilder(Register::class)->disableOriginalConstructor()->getMock();
        $registerForm->expects($this->once())
            ->method('setHydrator');
        $registerForm->expects($this->once())
            ->method('bind');
        $registerForm->expects($this->once())
            ->method('setData')
            ->with($expectArray);
        $registerForm->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($user));
        $registerForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->mapper->expects($this->once())
            ->method('insert')
            ->with($user)
            ->will($this->returnValue($user));

        $this->service->setRegisterForm($registerForm);

        $result = $this->service->register($expectArray);

        $this->assertSame($user, $result);
        $this->assertEquals(0, $user->getState());
    }

    /**
     * @covers ZfcUser\Service\User::changePassword
     */
    public function testChangePasswordWithWrongOldPassword(): void
    {
        $data = ['newCredential' => 'zfcUser', 'credential' => 'zfcUserOld'];

        $this->options->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->options->getPasswordCost());

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($bcrypt->create('wrongPassword')));

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $result = $this->service->changePassword($data);
        $this->assertFalse($result);
    }

    /**
     * @covers ZfcUser\Service\User::changePassword
     */
    public function testChangePassword(): void
    {
        $data = ['newCredential' => 'zfcUser', 'credential' => 'zfcUserOld'];

        $this->options->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->options->getPasswordCost());

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

        $this->mapper->expects($this->once())
            ->method('update')
            ->with($user);

        $result = $this->service->changePassword($data);
        $this->assertTrue($result);
    }

    /**
     * @covers ZfcUser\Service\User::changeEmail
     */
    public function testChangeEmail(): void
    {
        $data = ['credential' => 'zfcUser', 'newIdentity' => 'zfcUser@zfcUser.com'];

        $this->options->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->options->getPasswordCost());

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

        $this->mapper->expects($this->once())
            ->method('update')
            ->with($user);

        $result = $this->service->changeEmail($data);
        $this->assertTrue($result);
    }

    /**
     * @covers ZfcUser\Service\User::changeEmail
     */
    public function testChangeEmailWithWrongPassword(): void
    {
        $data = ['credential' => 'zfcUserOld'];

        $this->options->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->options->getPasswordCost());

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($bcrypt->create('wrongPassword')));

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $result = $this->service->changeEmail($data);
        $this->assertFalse($result);
    }

    /**
     * @covers ZfcUser\Service\User::getUserMapper
     */
    public function testGetUserMapper(): void
    {
        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('zfcuser_user_mapper')
            ->will($this->returnValue($this->mapper));

        $service = new Service();
        $service->setServiceManager($this->serviceManager);
        $this->assertInstanceOf(UserInterface::class, $service->getUserMapper());
    }

    /**
     * @covers ZfcUser\Service\User::getUserMapper
     * @covers ZfcUser\Service\User::setUserMapper
     */
    public function testSetGetUserMapper(): void
    {
        $this->assertSame($this->mapper, $this->service->getUserMapper());
    }

    /**
     * @covers ZfcUser\Service\User::getAuthService
     */
    public function testGetAuthService(): void
    {
        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('zfcuser_auth_service')
            ->will($this->returnValue($this->authService));

        $service = new Service();
        $service->setServiceManager($this->serviceManager);
        $this->assertInstanceOf(AuthenticationService::class, $service->getAuthService());
    }

    /**
     * @covers ZfcUser\Service\User::getAuthService
     * @covers ZfcUser\Service\User::setAuthService
     */
    public function testSetGetAuthService(): void
    {
        $this->assertSame($this->authService, $this->service->getAuthService());
    }

    /**
     * @covers ZfcUser\Service\User::getRegisterForm
     */
    public function testGetRegisterForm(): void
    {
        $form = $this->getMockBuilder(Register::class)->disableOriginalConstructor()->getMock();

        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('zfcuser_register_form')
            ->will($this->returnValue($form));

        $service = new Service();
        $service->setServiceManager($this->serviceManager);

        $result = $service->getRegisterForm();

        $this->assertInstanceOf(Register::class, $result);
        $this->assertSame($form, $result);
    }

    /**
     * @covers ZfcUser\Service\User::getRegisterForm
     * @covers ZfcUser\Service\User::setRegisterForm
     */
    public function testSetGetRegisterForm(): void
    {
        $form = $this->getMockBuilder(Register::class)->disableOriginalConstructor()->getMock();
        $this->service->setRegisterForm($form);

        $this->assertSame($form, $this->service->getRegisterForm());
    }

    /**
     * @covers ZfcUser\Service\User::getChangePasswordForm
     */
    public function testGetChangePasswordForm(): void
    {
        $form = $this->getMockBuilder(ChangePassword::class)->disableOriginalConstructor()->getMock();

        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('zfcuser_change_password_form')
            ->will($this->returnValue($form));

        $service = new Service();
        $service->setServiceManager($this->serviceManager);
        $this->assertInstanceOf(ChangePassword::class, $service->getChangePasswordForm());
    }

    /**
     * @covers ZfcUser\Service\User::getChangePasswordForm
     * @covers ZfcUser\Service\User::setChangePasswordForm
     */
    public function testSetGetChangePasswordForm(): void
    {
        $form = $this->getMockBuilder(ChangePassword::class)->disableOriginalConstructor()->getMock();
        $this->service->setChangePasswordForm($form);

        $this->assertSame($form, $this->service->getChangePasswordForm());
    }

    /**
     * @covers ZfcUser\Service\User::getOptions
     */
    public function testGetOptions(): void
    {
        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('zfcuser_module_options')
            ->will($this->returnValue($this->options));

        $service = new Service();
        $service->setServiceManager($this->serviceManager);
        $this->assertInstanceOf(ModuleOptions::class, $service->getOptions());
    }

    /**
     * @covers ZfcUser\Service\User::setOptions
     */
    public function testSetOptions(): void
    {
        $this->assertSame($this->options, $this->service->getOptions());
    }

    /**
     * @covers ZfcUser\Service\User::getServiceManager
     * @covers ZfcUser\Service\User::setServiceManager
     */
    public function testSetGetServiceManager(): void
    {
        $this->assertSame($this->serviceManager, $this->service->getServiceManager());
    }

    /**
     * @covers ZfcUser\Service\User::getFormHydrator
     */
    public function testGetFormHydrator(): void
    {
        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('zfcuser_register_form_hydrator')
            ->will($this->returnValue($this->formHydrator));

        $service = new Service();
        $service->setServiceManager($this->serviceManager);
        $this->assertInstanceOf(HydratorInterface::class, $service->getFormHydrator());
    }

    /**
     * @covers ZfcUser\Service\User::setFormHydrator
     */
    public function testSetFormHydrator(): void
    {
        $this->assertSame($this->formHydrator, $this->service->getFormHydrator());
    }
}
