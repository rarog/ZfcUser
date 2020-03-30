<?php

namespace UserAuthenticatorTest\Options;

use PHPUnit\Framework\TestCase;
use UserAuthenticator\Authentication\Adapter\Db;
use UserAuthenticator\Model\User;
use UserAuthenticator\Options\ModuleOptions as Options;

class ModuleOptionsTest extends TestCase
{
    /**
     * @var Options $options
     */
    protected $options;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->options = new Options();
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->options);
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getLoginRedirectRoute
     * @covers UserAuthenticator\Options\ModuleOptions::setLoginRedirectRoute
     */
    public function testSetGetLoginRedirectRoute()
    {
        $this->options->setLoginRedirectRoute('zfcUserRoute');
        $this->assertEquals('zfcUserRoute', $this->options->getLoginRedirectRoute());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getLoginRedirectRoute
     */
    public function testGetLoginRedirectRoute()
    {
        $this->assertEquals('zfcuser', $this->options->getLoginRedirectRoute());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getLogoutRedirectRoute
     * @covers UserAuthenticator\Options\ModuleOptions::setLogoutRedirectRoute
     */
    public function testSetGetLogoutRedirectRoute()
    {
        $this->options->setLogoutRedirectRoute('zfcUserRoute');
        $this->assertEquals('zfcUserRoute', $this->options->getLogoutRedirectRoute());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getLogoutRedirectRoute
     */
    public function testGetLogoutRedirectRoute()
    {
        $this->assertSame('zfcuser/login', $this->options->getLogoutRedirectRoute());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUseRedirectParameterIfPresent
     * @covers UserAuthenticator\Options\ModuleOptions::setUseRedirectParameterIfPresent
     */
    public function testSetGetUseRedirectParameterIfPresent()
    {
        $this->options->setUseRedirectParameterIfPresent(false);
        $this->assertFalse($this->options->getUseRedirectParameterIfPresent());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUseRedirectParameterIfPresent
     */
    public function testGetUseRedirectParameterIfPresent()
    {
        $this->assertTrue($this->options->getUseRedirectParameterIfPresent());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUserLoginWidgetViewTemplate
     * @covers UserAuthenticator\Options\ModuleOptions::setUserLoginWidgetViewTemplate
     */
    public function testSetGetUserLoginWidgetViewTemplate()
    {
        $this->options->setUserLoginWidgetViewTemplate('zfcUser.phtml');
        $this->assertEquals('zfcUser.phtml', $this->options->getUserLoginWidgetViewTemplate());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUserLoginWidgetViewTemplate
     */
    public function testGetUserLoginWidgetViewTemplate()
    {
        $this->assertEquals('zfc-user/user/login.phtml', $this->options->getUserLoginWidgetViewTemplate());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getEnableRegistration
     * @covers UserAuthenticator\Options\ModuleOptions::setEnableRegistration
     */
    public function testSetGetEnableRegistration()
    {
        $this->options->setEnableRegistration(false);
        $this->assertFalse($this->options->getEnableRegistration());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getEnableRegistration
     */
    public function testGetEnableRegistration()
    {
        $this->assertTrue($this->options->getEnableRegistration());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getLoginFormTimeout
     * @covers UserAuthenticator\Options\ModuleOptions::setLoginFormTimeout
     */
    public function testSetGetLoginFormTimeout()
    {
        $this->options->setLoginFormTimeout(100);
        $this->assertEquals(100, $this->options->getLoginFormTimeout());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getLoginFormTimeout
     */
    public function testGetLoginFormTimeout()
    {
        $this->assertEquals(300, $this->options->getLoginFormTimeout());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUserFormTimeout
     * @covers UserAuthenticator\Options\ModuleOptions::setUserFormTimeout
     */
    public function testSetGetUserFormTimeout()
    {
        $this->options->setUserFormTimeout(100);
        $this->assertEquals(100, $this->options->getUserFormTimeout());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUserFormTimeout
     */
    public function testGetUserFormTimeout()
    {
        $this->assertEquals(300, $this->options->getUserFormTimeout());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getLoginAfterRegistration
     * @covers UserAuthenticator\Options\ModuleOptions::setLoginAfterRegistration
     */
    public function testSetGetLoginAfterRegistration()
    {
        $this->options->setLoginAfterRegistration(false);
        $this->assertFalse($this->options->getLoginAfterRegistration());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getLoginAfterRegistration
     */
    public function testGetLoginAfterRegistration()
    {
        $this->assertTrue($this->options->getLoginAfterRegistration());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getEnableUserState
     * @covers UserAuthenticator\Options\ModuleOptions::setEnableUserState
     */
    public function testSetGetEnableUserState()
    {
        $this->options->setEnableUserState(true);
        $this->assertTrue($this->options->getEnableUserState());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getEnableUserState
     */
    public function testGetEnableUserState()
    {
        $this->assertFalse($this->options->getEnableUserState());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getDefaultUserState
     */
    public function testGetDefaultUserState()
    {
        $this->assertEquals(1, $this->options->getDefaultUserState());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getDefaultUserState
     * @covers UserAuthenticator\Options\ModuleOptions::setDefaultUserState
     */
    public function testSetGetDefaultUserState()
    {
        $this->options->setDefaultUserState(3);
        $this->assertEquals(3, $this->options->getDefaultUserState());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getAllowedLoginStates
     */
    public function testGetAllowedLoginStates()
    {
        $this->assertEquals([null, 1], $this->options->getAllowedLoginStates());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getAllowedLoginStates
     * @covers UserAuthenticator\Options\ModuleOptions::setAllowedLoginStates
     */
    public function testSetGetAllowedLoginStates()
    {
        $this->options->setAllowedLoginStates([2, 5, null]);
        $this->assertEquals([2, 5, null], $this->options->getAllowedLoginStates());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getAuthAdapters
     */
    public function testGetAuthAdapters()
    {
        $this->assertEquals([100 => Db::class], $this->options->getAuthAdapters());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getAuthAdapters
     * @covers UserAuthenticator\Options\ModuleOptions::setAuthAdapters
     */
    public function testSetGetAuthAdapters()
    {
        $this->options->setAuthAdapters([40 => 'SomeAdapter']);
        $this->assertEquals([40 => 'SomeAdapter'], $this->options->getAuthAdapters());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getAuthIdentityFields
     * @covers UserAuthenticator\Options\ModuleOptions::setAuthIdentityFields
     */
    public function testSetGetAuthIdentityFields()
    {
        $this->options->setAuthIdentityFields(['username']);
        $this->assertEquals(['username'], $this->options->getAuthIdentityFields());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getAuthIdentityFields
     */
    public function testGetAuthIdentityFields()
    {
        $this->assertEquals(['email'], $this->options->getAuthIdentityFields());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getEnableUsername
     */
    public function testGetEnableUsername()
    {
        $this->assertFalse($this->options->getEnableUsername());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getEnableUsername
     * @covers UserAuthenticator\Options\ModuleOptions::setEnableUsername
     */
    public function testSetGetEnableUsername()
    {
        $this->options->setEnableUsername(true);
        $this->assertTrue($this->options->getEnableUsername());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getEnableDisplayName
     * @covers UserAuthenticator\Options\ModuleOptions::setEnableDisplayName
     */
    public function testSetGetEnableDisplayName()
    {
        $this->options->setEnableDisplayName(true);
        $this->assertTrue($this->options->getEnableDisplayName());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getEnableDisplayName
     */
    public function testGetEnableDisplayName()
    {
        $this->assertFalse($this->options->getEnableDisplayName());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUseRegistrationFormCaptcha
     * @covers UserAuthenticator\Options\ModuleOptions::setUseRegistrationFormCaptcha
     */
    public function testSetGetUseRegistrationFormCaptcha()
    {
        $this->options->setUseRegistrationFormCaptcha(true);
        $this->assertTrue($this->options->getUseRegistrationFormCaptcha());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUseRegistrationFormCaptcha
     */
    public function testGetUseRegistrationFormCaptcha()
    {
        $this->assertFalse($this->options->getUseRegistrationFormCaptcha());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUseLoginFormCaptcha
     * @covers UserAuthenticator\Options\ModuleOptions::setUseLoginFormCaptcha
     */
    public function testSetGetUseLoginFormCaptcha()
    {
        $this->options->setUseLoginFormCaptcha(true);
        $this->assertTrue($this->options->getUseLoginFormCaptcha());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUseLoginFormCaptcha
     */
    public function testGetUseLoginFormCaptcha()
    {
        $this->assertFalse($this->options->getUseLoginFormCaptcha());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUserEntityClass
     * @covers UserAuthenticator\Options\ModuleOptions::setUserEntityClass
     */
    public function testSetGetUserEntityClass()
    {
        $this->options->setUserEntityClass('zfcUser');
        $this->assertEquals('zfcUser', $this->options->getUserEntityClass());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getUserEntityClass
     */
    public function testGetUserEntityClass()
    {
        $this->assertEquals(User::class, $this->options->getUserEntityClass());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getPasswordCost
     * @covers UserAuthenticator\Options\ModuleOptions::setPasswordCost
     */
    public function testSetGetPasswordCost()
    {
        $this->options->setPasswordCost(10);
        $this->assertEquals(10, $this->options->getPasswordCost());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getPasswordCost
     */
    public function testGetPasswordCost()
    {
        $this->assertEquals(14, $this->options->getPasswordCost());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getTableName
     * @covers UserAuthenticator\Options\ModuleOptions::setTableName
     */
    public function testSetGetTableName()
    {
        $this->options->setTableName('zfcUser');
        $this->assertEquals('zfcUser', $this->options->getTableName());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getTableName
     */
    public function testGetTableName()
    {
        $this->assertEquals('user', $this->options->getTableName());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getFormCaptchaOptions
     * @covers UserAuthenticator\Options\ModuleOptions::setFormCaptchaOptions
     */
    public function testSetGetFormCaptchaOptions()
    {
        $expected = [
            'class' => 'someClass',
            'options' => [
                'anOption' => 3,
            ],
        ];
        $this->options->setFormCaptchaOptions($expected);
        $this->assertEquals($expected, $this->options->getFormCaptchaOptions());
    }

    /**
     * @covers UserAuthenticator\Options\ModuleOptions::getFormCaptchaOptions
     */
    public function testGetFormCaptchaOptions()
    {
        $expected = [
            'class' => 'figlet',
            'options' => [
                'wordLen' => 5,
                'expiration' => 300,
                'timeout' => 300,
            ],
        ];
        $this->assertEquals($expected, $this->options->getFormCaptchaOptions());
    }
}
