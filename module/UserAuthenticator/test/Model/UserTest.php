<?php

namespace UserAuthenticatorTest\Model;

use PHPUnit\Framework\TestCase;
use UserAuthenticator\Model\User;

class UserTest extends TestCase
{
    protected $user;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->user = new User();
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->user);
    }

    /**
     * @covers UserAuthenticator\Model\User::setId
     * @covers UserAuthenticator\Model\User::getId
     */
    public function testSetGetId()
    {
        $this->user->setId(1);
        $this->assertEquals(1, $this->user->getId());
    }

    /**
     * @covers UserAuthenticator\Model\User::setUsername
     * @covers UserAuthenticator\Model\User::getUsername
     */
    public function testSetGetUsername()
    {
        $this->user->setUsername('zfcUser');
        $this->assertEquals('zfcUser', $this->user->getUsername());
    }

    /**
     * @covers UserAuthenticator\Model\User::setDisplayName
     * @covers UserAuthenticator\Model\User::getDisplayName
     */
    public function testSetGetDisplayName()
    {
        $this->user->setDisplayName('Zfc User');
        $this->assertEquals('Zfc User', $this->user->getDisplayName());
    }

    /**
     * @covers UserAuthenticator\Model\User::setEmail
     * @covers UserAuthenticator\Model\User::getEmail
     */
    public function testSetGetEmail()
    {
        $this->user->setEmail('zfcUser@zfcUser.com');
        $this->assertEquals('zfcUser@zfcUser.com', $this->user->getEmail());
    }

    /**
     * @covers UserAuthenticator\Model\User::setPassword
     * @covers UserAuthenticator\Model\User::getPassword
     */
    public function testSetGetPassword()
    {
        $this->user->setPassword('zfcUser');
        $this->assertEquals('zfcUser', $this->user->getPassword());
    }

    /**
     * @covers UserAuthenticator\Model\User::setState
     * @covers UserAuthenticator\Model\User::getState
     */
    public function testSetGetState()
    {
        $this->user->setState(1);
        $this->assertEquals(1, $this->user->getState());
    }
}
