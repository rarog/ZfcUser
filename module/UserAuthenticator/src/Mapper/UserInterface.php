<?php

namespace UserAuthenticator\Mapper;

interface UserInterface
{
    /**
     * @param $email
     * @return \UserAuthenticator\Model\UserInterface
     */
    public function findByEmail($email);

    /**
     * @param string $username
     * @return \UserAuthenticator\Model\UserInterface
     */
    public function findByUsername($username);

    /**
     * @param string|int $id
     * @return \UserAuthenticator\Model\UserInterface
     */
    public function findById($id);

    /**
     * @return \UserAuthenticator\Model\UserInterface $user
     */
    public function insert(\UserAuthenticator\Model\UserInterface $user);

    /**
     * @return \UserAuthenticator\Model\UserInterface $user
     */
    public function update(\UserAuthenticator\Model\UserInterface $user);
}
