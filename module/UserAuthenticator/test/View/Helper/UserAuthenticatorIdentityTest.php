<?php

namespace UserAuthenticatorTest\View\Helper;

use Laminas\Authentication\AuthenticationService;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\View\Helper\UserAuthenticatorIdentity as ViewHelper;

class UserAuthenticatorIdentityTest extends TestCase
{
    protected $helper;

    protected $authService;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $helper = new ViewHelper();
        $this->helper = $helper;

        $authService = $this->getMockBuilder(AuthenticationService::class)
            ->getMock();
        $this->authService = $authService;

        $helper->setAuthService($authService);
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->authService);
        unset($this->helper);
    }

    /**
     * @covers UserAuthenticator\View\Helper\UserAuthenticatorIdentity::__invoke
     */
    public function testInvokeWithIdentity()
    {
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));
        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue('zfcUser'));

        $result = $this->helper->__invoke();

        $this->assertEquals('zfcUser', $result);
    }

    /**
     * @covers UserAuthenticator\View\Helper\UserAuthenticatorIdentity::__invoke
     */
    public function testInvokeWithoutIdentity()
    {
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $result = $this->helper->__invoke();

        $this->assertFalse($result);
    }

    /**
     * @covers UserAuthenticator\View\Helper\UserAuthenticatorIdentity::setAuthService
     * @covers UserAuthenticator\View\Helper\UserAuthenticatorIdentity::getAuthService
     */
    public function testSetGetAuthService()
    {
        //We set the authservice in setUp, so we dont have to set it again
        $this->assertSame($this->authService, $this->helper->getAuthService());
    }
}
