<?php

namespace ZfcUserTest\MOdule;

use PHPUnit\Framework\TestCase;
use ZfcUser\Model\User;

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
     * @covers ZfcUser\Model\User::setId
     * @covers ZfcUser\Model\User::getId
     */
    public function testSetGetId()
    {
        $this->user->setId(1);
        $this->assertEquals(1, $this->user->getId());
    }

    /**
     * @covers ZfcUser\Model\User::setUsername
     * @covers ZfcUser\Model\User::getUsername
     */
    public function testSetGetUsername()
    {
        $this->user->setUsername('zfcUser');
        $this->assertEquals('zfcUser', $this->user->getUsername());
    }

    /**
     * @covers ZfcUser\Model\User::setDisplayName
     * @covers ZfcUser\Model\User::getDisplayName
     */
    public function testSetGetDisplayName()
    {
        $this->user->setDisplayName('Zfc User');
        $this->assertEquals('Zfc User', $this->user->getDisplayName());
    }

    /**
     * @covers ZfcUser\Model\User::setEmail
     * @covers ZfcUser\Model\User::getEmail
     */
    public function testSetGetEmail()
    {
        $this->user->setEmail('zfcUser@zfcUser.com');
        $this->assertEquals('zfcUser@zfcUser.com', $this->user->getEmail());
    }

    /**
     * @covers ZfcUser\Model\User::setPassword
     * @covers ZfcUser\Model\User::getPassword
     */
    public function testSetGetPassword()
    {
        $this->user->setPassword('zfcUser');
        $this->assertEquals('zfcUser', $this->user->getPassword());
    }

    /**
     * @covers ZfcUser\Model\User::setState
     * @covers ZfcUser\Model\User::getState
     */
    public function testSetGetState()
    {
        $this->user->setState(1);
        $this->assertEquals(1, $this->user->getState());
    }
}
