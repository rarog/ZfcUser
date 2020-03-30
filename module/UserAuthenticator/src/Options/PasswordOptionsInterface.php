<?php

namespace UserAuthenticator\Options;

interface PasswordOptionsInterface
{
    /**
     * set password cost
     *
     * @param int $cost
     * @return ModuleOptions
     */
    public function setPasswordCost($cost);

    /**
     * get password cost
     *
     * @return int
     */
    public function getPasswordCost();
}
