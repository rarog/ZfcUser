<?php

namespace ZfcUserTest\Mapper;

use PHPUnit\Framework\TestCase;
use ZfcUser\Entity\User;
use ZfcUser\Mapper\UserHydrator as Hydrator;
use stdClass;
use ZfcUser\Mapper\Exception\InvalidArgumentException;

class UserHydratorTest extends TestCase
{
    protected $hydrator;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->hydrator = new Hydrator();
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->hydrator);
    }

    /**
     * @covers ZfcUser\Mapper\UserHydrator::extract
     */
    public function testExtractWithInvalidUserObject(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user = new stdClass();
        $this->hydrator->extract($user);
    }

    /**
     * @covers ZfcUser\Mapper\UserHydrator::extract
     * @covers ZfcUser\Mapper\UserHydrator::mapField
     * @dataProvider dataProviderTestExtractWithValidUserObject
     * @see https://github.com/ZF-Commons/ZfcUser/pull/421
     */
    public function testExtractWithValidUserObject($object, $expectArray): void
    {
        $result = $this->hydrator->extract($object);
        $this->assertEquals($expectArray, $result);
    }

    /**
     * @covers ZfcUser\Mapper\UserHydrator::hydrate
     */
    public function testHydrateWithInvalidUserObject(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user = new stdClass();
        $this->hydrator->hydrate([], $user);
    }

    /**
     * @covers ZfcUser\Mapper\UserHydrator::hydrate
     * @covers ZfcUser\Mapper\UserHydrator::mapField
     */
    public function testHydrateWithValidUserObject(): void
    {
        $user = new User();

        $expectArray = [
            'username' => 'zfcuser',
            'email' => 'Zfc User',
            'display_name' => 'ZfcUser',
            'password' => 'ZfcUserPassword',
            'state' => 1,
            'user_id' => 1
        ];

        $result = $this->hydrator->hydrate($expectArray, $user);

        $this->assertEquals($expectArray['username'], $result->getUsername());
        $this->assertEquals($expectArray['email'], $result->getEmail());
        $this->assertEquals($expectArray['display_name'], $result->getDisplayName());
        $this->assertEquals($expectArray['password'], $result->getPassword());
        $this->assertEquals($expectArray['state'], $result->getState());
        $this->assertEquals($expectArray['user_id'], $result->getId());
    }

    public function dataProviderTestExtractWithValidUserObject(): array
    {
        $createUserObject = function ($data) {
            $user = new User();
            foreach ($data as $key => $value) {
                if ($key == 'user_id') {
                    $key = 'id';
                }
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                call_user_func([$user, $method], $value);
            }
            return $user;
        };
        $return = [];

        $buffer = [
            'username' => 'zfcuser',
            'email' => 'Zfc User',
            'display_name' => 'ZfcUser',
            'password' => 'ZfcUserPassword',
            'state' => 1,
            'user_id' => 1
        ];

        $return[] = [$createUserObject($buffer), $buffer];

        /**
         * @see https://github.com/ZF-Commons/ZfcUser/pull/421
         */
        $buffer = [
            'username' => 'zfcuser',
            'email' => 'Zfc User',
            'display_name' => 'ZfcUser',
            'password' => 'ZfcUserPassword',
            'state' => 1
        ];

        $return[] = [$createUserObject($buffer), $buffer];

        return $return;
    }
}
