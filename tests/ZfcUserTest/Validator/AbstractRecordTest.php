<?php

namespace ZfcUserTest\Validator;

use PHPUnit\Framework\TestCase;
use ZfcUserTest\Validator\TestAsset\AbstractRecordExtension;
use ZfcUser\Mapper\UserInterface;
use ZfcUser\Validator\Exception\InvalidArgumentException;
use Exception;
use ReflectionMethod;

class AbstractRecordTest extends TestCase
{
    /**
     * @covers ZfcUser\Validator\AbstractRecord::__construct
     */
    public function testConstruct(): void
    {
        $options = ['key' => 'value'];
        $validator = new AbstractRecordExtension($options);
        $this->assertInstanceOf(AbstractRecordExtension::class, $validator);
    }

    /**
     * @covers ZfcUser\Validator\AbstractRecord::__construct
     */
    public function testConstructEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No key provided');

        $options = [];
        new AbstractRecordExtension($options);
    }

    /**
     * @covers ZfcUser\Validator\AbstractRecord::getMapper
     * @covers ZfcUser\Validator\AbstractRecord::setMapper
     */
    public function testGetSetMapper(): void
    {
        $options = ['key' => ''];
        $validator = new AbstractRecordExtension($options);

        $this->assertNull($validator->getMapper());

        $mapper = $this->getMockBuilder(UserInterface::class)
            ->getMock();
        $validator->setMapper($mapper);
        $this->assertSame($mapper, $validator->getMapper());
    }

    /**
     * @covers ZfcUser\Validator\AbstractRecord::getKey
     * @covers ZfcUser\Validator\AbstractRecord::setKey
     */
    public function testGetSetKey()
    {
        $options = ['key' => 'username'];
        $validator = new AbstractRecordExtension($options);

        $this->assertEquals('username', $validator->getKey());

        $validator->setKey('email');
        $this->assertEquals('email', $validator->getKey());
    }

    /**
     * @covers ZfcUser\Validator\AbstractRecord::query
     */
    public function testQueryWithInvalidKey(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid key used in ZfcUser validator');

        $options = ['key' => 'zfcUser'];
        $validator = new AbstractRecordExtension($options);

        $method = new ReflectionMethod(AbstractRecordExtension::class, 'query');
        $method->setAccessible(true);

        $method->invoke($validator, ['test']);
    }

    /**
     * @covers ZfcUser\Validator\AbstractRecord::query
     */
    public function testQueryWithKeyUsername(): void
    {
        $options = ['key' => 'username'];
        $validator = new AbstractRecordExtension($options);

        $mapper = $this->getMockBuilder(UserInterface::class)
            ->getMock();
        $mapper->expects($this->once())
               ->method('findByUsername')
               ->with('test')
               ->will($this->returnValue('ZfcUser'));

        $validator->setMapper($mapper);

        $method = new ReflectionMethod(AbstractRecordExtension::class, 'query');
        $method->setAccessible(true);

        $result = $method->invoke($validator, 'test');

        $this->assertEquals('ZfcUser', $result);
    }

    /**
     * @covers ZfcUser\Validator\AbstractRecord::query
     */
    public function testQueryWithKeyEmail(): void
    {
        $options = ['key' => 'email'];
        $validator = new AbstractRecordExtension($options);

        $mapper = $this->getMockBuilder(UserInterface::class)
            ->getMock();
        $mapper->expects($this->once())
            ->method('findByEmail')
            ->with('test@test.com')
            ->will($this->returnValue('ZfcUser'));

        $validator->setMapper($mapper);

        $method = new ReflectionMethod(AbstractRecordExtension::class, 'query');
        $method->setAccessible(true);

        $result = $method->invoke($validator, 'test@test.com');

        $this->assertEquals('ZfcUser', $result);
    }
}
