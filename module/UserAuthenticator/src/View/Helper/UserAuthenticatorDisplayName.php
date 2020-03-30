<?php

namespace UserAuthenticator\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Authentication\AuthenticationService;
use UserAuthenticator\Model\UserInterface as User;

class UserAuthenticatorDisplayName extends AbstractHelper
{
    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * __invoke
     *
     * @access public
     * @return \UserAuthenticator\Model\UserInterface $user
     * @throws \UserAuthenticator\Exception\DomainException
     * @return String
     */
    public function __invoke(User $user = null)
    {
        if (null === $user) {
            if ($this->getAuthService()->hasIdentity()) {
                $user = $this->getAuthService()->getIdentity();
                if (! $user instanceof User) {
                    throw new \UserAuthenticator\Exception\DomainException(
                        '$user is not an instance of User',
                        500
                    );
                }
            } else {
                return false;
            }
        }

        $displayName = $user->getDisplayName();
        if (null === $displayName) {
            $displayName = $user->getUsername();
        }
        // User will always have an email, so we do not have to throw error
        if (null === $displayName) {
            $displayName = $user->getEmail();
            $displayName = substr($displayName, 0, strpos($displayName, '@'));
        }

        return $displayName;
    }

    /**
     * Get authService.
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * Set authService.
     *
     * @param AuthenticationService $authService
     * @return \UserAuthenticator\View\Helper\UserAuthenticatorDisplayName
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }
}
