<?php

namespace ZfcUser\Mapper;

interface UserInterface
{
    /**
     * @param $email
     * @return \ZfcUser\Model\UserInterface
     */
    public function findByEmail($email);

    /**
     * @param string $username
     * @return \ZfcUser\Model\UserInterface
     */
    public function findByUsername($username);

    /**
     * @param string|int $id
     * @return \ZfcUser\Model\UserInterface
     */
    public function findById($id);

    /**
     * @param \ZfcUser\Model\UserInterface $user
     */
    public function insert(\ZfcUser\Model\UserInterface $user);

    /**
     * @param \ZfcUser\Model\UserInterface $user
     */
    public function update(\ZfcUser\Model\UserInterface $user);
}
