<?php

namespace UserAuthenticator\Service;

use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\EventManager\EventManagerAwareTrait;
use UserAuthenticator\Mapper\User as UserMapper;
use UserAuthenticator\Model\User;
use UserAuthenticator\Options\ModuleOptions;

class UserService
{
    use EventManagerAwareTrait;

    /**
     * @var UserMapper
     */
    protected $userMapper;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    public function __construct(
        UserMapper $userMapper,
        AuthenticationService $authService,
        ModuleOptions $moduleOptions
    ) {
        $this->userMapper = $userMapper;
        $this->authService = $authService;
        $this->moduleOptions = $moduleOptions;
    }

    /**
     * @param User $user
     * @return User
     */
    public function register(User $user)
    {
        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->moduleOptions->getPasswordCost());
        $user->setPassword($bcrypt->create($user->getPassword()));

        // If user state is enabled, set the default state value
        if ($this->moduleOptions->getEnableUserState()) {
            $user->setState($this->moduleOptions->getDefaultUserState());
        }
        $this->getEventManager()->trigger(__FUNCTION__, $this, ['user' => $user]);
        $this->userMapper->insert($user);
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, ['user' => $user]);
        return $user;
    }

    /**
     * @param array $data
     * @return boolean
     */
    public function changePassword(array $data)
    {
        $currentUser = $this->authService->getIdentity();

        $oldPass = $data['credential'];
        $newPass = $data['newCredential'];

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->moduleOptions->getPasswordCost());

        if (! $bcrypt->verify($oldPass, $currentUser->getPassword())) {
            return false;
        }

        $pass = $bcrypt->create($newPass);
        $currentUser->setPassword($pass);

        $this->getEventManager()->trigger(__FUNCTION__, $this, ['user' => $currentUser]);
        $this->userMapper->update($currentUser);
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, ['user' => $currentUser]);

        return true;
    }

    /**
     * @param array $data
     * @return boolean
     */
    public function changeEmail(array $data)
    {
        $currentUser = $this->authService->getIdentity();

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->moduleOptions->getPasswordCost());

        if (! $bcrypt->verify($data['credential'], $currentUser->getPassword())) {
            return false;
        }

        $currentUser->setEmail($data['newIdentity']);

        $this->getEventManager()->trigger(__FUNCTION__, $this, ['user' => $currentUser]);
        $this->userMapper->update($currentUser);
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, ['user' => $currentUser]);

        return true;
    }
}
