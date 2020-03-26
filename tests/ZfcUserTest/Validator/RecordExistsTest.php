<?php

namespace ZfcUserTest\Validator;

use PHPUnit\Framework\TestCase;
use ZfcUser\Mapper\UserInterface;
use ZfcUser\Validator\AbstractRecord;
use ZfcUser\Validator\RecordExists as Validator;

class RecordExistsTest extends TestCase
{
    protected $validator;

    protected $mapper;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $options = ['key' => 'username'];
        $validator = new Validator($options);
        $this->validator = $validator;

        $mapper = $this->getMockBuilder(UserInterface::class)
            ->getMock();
        $this->mapper = $mapper;

        $validator->setMapper($mapper);
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->mapper);
        unset($this->validator);
    }

    /**
     * @covers ZfcUser\Validator\RecordExists::isValid
     */
    public function testIsValid(): void
    {
        $this->mapper->expects($this->once())
            ->method('findByUsername')
            ->with('zfcUser')
            ->will($this->returnValue('zfcUser'));

        $result = $this->validator->isValid('zfcUser');
        $this->assertTrue($result);
    }

    /**
     * @covers ZfcUser\Validator\RecordExists::isValid
     */
    public function testIsInvalid(): void
    {
        $this->mapper->expects($this->once())
            ->method('findByUsername')
            ->with('zfcUser')
            ->will($this->returnValue(false));

        $result = $this->validator->isValid('zfcUser');
        $this->assertFalse($result);

        $options = $this->validator->getOptions();
        $this->assertArrayHasKey(AbstractRecord::ERROR_NO_RECORD_FOUND, $options['messages']);
        $this->assertEquals(
            $options['messageTemplates'][AbstractRecord::ERROR_NO_RECORD_FOUND],
            $options['messages'][AbstractRecord::ERROR_NO_RECORD_FOUND]
        );
    }
}
