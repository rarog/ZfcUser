<?php

namespace UserAuthenticator\Controller;

use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Stdlib\Parameters;
use Laminas\Stdlib\ResponseInterface as Response;
use Laminas\View\Model\ViewModel;
use UserAuthenticator\Form\ChangeEmailForm;
use UserAuthenticator\Form\ChangePasswordForm;
use UserAuthenticator\Form\LoginForm;
use UserAuthenticator\Form\RegisterForm;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Service\UserService;

class UserController extends AbstractActionController
{
    public const ROUTE_CHANGEPASSWD = 'zfcuser/changepassword';
    public const ROUTE_LOGIN        = 'zfcuser/login';
    public const ROUTE_REGISTER     = 'zfcuser/register';
    public const ROUTE_CHANGEEMAIL  = 'zfcuser/changeemail';

    public const CONTROLLER_NAME    = 'zfcuser';

    /**
     * @var RedirectCallback
     */
    private $redirectCallback;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var ChangeEmailForm
     */
    private $changeEmailForm;

    /**
     * @var ChangePasswordForm
     */
    private $changePasswordForm;

    /**
     * @var LoginForm
     */
    private $loginForm;

    /**
     * @var RegisterForm
     */
    private $registerForm;

    /**
     * @var ModuleOptions
     */
    private $moduleOptions;

    /**
     * @todo Make this dynamic / translation-friendly
     * @var string
     */
    private $failedLoginMessage = 'Authentication failed. Please try again.';

    /**
     * @param RedirectCallback $redirectCallback
     */
    public function __construct(
        RedirectCallback $redirectCallback,
        UserService $userService,
        ChangeEmailForm $changeEmailForm,
        ChangePasswordForm $changePasswordForm,
        LoginForm $loginForm,
        RegisterForm $registerForm,
        ModuleOptions $moduleOptions
    ) {
        $this->redirectCallback = $redirectCallback;
        $this->userService = $userService;
        $this->changeEmailForm = $changeEmailForm;
        $this->changePasswordForm = $changePasswordForm;
        $this->loginForm = $loginForm;
        $this->registerForm = $registerForm;
        $this->moduleOptions = $moduleOptions;
    }

    /**
     * User page
     */
    public function indexAction()
    {
        if (! $this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        return new ViewModel();
    }

    /**
     * Login form
     */
    public function loginAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->moduleOptions->getLoginRedirectRoute());
        }

        $request = $this->getRequest();

        if ($this->moduleOptions->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        if (! $request->isPost()) {
            return [
                'loginForm' => $this->loginForm,
                'redirect' => $redirect,
                'enableRegistration' => $this->moduleOptions->getEnableRegistration(),
            ];
        }

        $this->loginForm->setData($request->getPost());

        if (! $this->loginForm->isValid()) {
            $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
            return $this->redirect()->toUrl(
                $this->url()->fromRoute(static::ROUTE_LOGIN) .
                    ($redirect ? '?redirect=' . rawurlencode($redirect) : '')
            );
        }

        // clear adapters
        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();

        return $this->forward()->dispatch(static::CONTROLLER_NAME, ['action' => 'authenticate']);
    }

    /**
     * Logout and clear the identity
     */
    public function logoutAction()
    {
        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->zfcUserAuthentication()->getAuthAdapter()->logoutAdapters();
        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();

        $redirect = $this->redirectCallback;

        return $redirect();
    }

    /**
     * General-purpose authentication action
     */
    public function authenticateAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->moduleOptions->getLoginRedirectRoute());
        }

        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        $result = $adapter->prepareForAuthentication($this->getRequest());

        // Return early if an adapter returned a response
        if ($result instanceof Response) {
            return $result;
        }

        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);

        if (! $auth->isValid()) {
            $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
            $adapter->resetAdapters();
            return $this->redirect()->toUrl(
                $this->url()->fromRoute(static::ROUTE_LOGIN) .
                ($redirect ? '?redirect=' . rawurlencode($redirect) : '')
            );
        }

        $redirect = $this->redirectCallback;

        return $redirect();
    }

    /**
     * Register new user
     */
    public function registerAction()
    {
        // if the user is logged in, we don't need to register
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->moduleOptions->getLoginRedirectRoute());
        }
        // if registration is disabled
        if (! $this->moduleOptions->getEnableRegistration()) {
            return ['enableRegistration' => false];
        }

        $request = $this->getRequest();
        $service = $this->userService;

        if ($this->moduleOptions->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        $redirectUrl = $this->url()->fromRoute(static::ROUTE_REGISTER)
            . ($redirect ? '?redirect=' . rawurlencode($redirect) : '');
        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return [
                'registerForm' => $this->registerForm,
                'enableRegistration' => $this->moduleOptions->getEnableRegistration(),
                'redirect' => $redirect,
            ];
        }

        $post = $prg;
        $redirect = isset($prg['redirect']) ? $prg['redirect'] : null;

        $class = $this->moduleOptions->getUserEntityClass();
        $user = new $class();
        $this->registerForm->setHydrator(new ClassMethodsHydrator());
        $this->registerForm->bind($user);
        $this->registerForm->setData($post);
        if (! $this->registerForm->isValid()) {
            return [
                'registerForm' => $this->registerForm,
                'enableRegistration' => $this->moduleOptions->getEnableRegistration(),
                'redirect' => $redirect,
            ];
        }

        $user = $service->register($this->registerForm->getData());

        if ($this->moduleOptions->getLoginAfterRegistration()) {
            $identityFields = $this->moduleOptions->getAuthIdentityFields();
            if (in_array('email', $identityFields)) {
                $post['identity'] = $user->getEmail();
            } elseif (in_array('username', $identityFields)) {
                $post['identity'] = $user->getUsername();
            }
            $post['credential'] = $post['password'];
            $request->setPost(new Parameters($post));
            return $this->forward()->dispatch(static::CONTROLLER_NAME, ['action' => 'authenticate']);
        }

        // TODO: Add the redirect parameter here...
        return $this->redirect()->toUrl(
            $this->url()->fromRoute(static::ROUTE_LOGIN) . ($redirect ? '?redirect=' . rawurlencode($redirect) : '')
        );
    }

    /**
     * Change the users password
     */
    public function changepasswordAction()
    {
        // if the user isn't logged in, we can't change password
        if (! $this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->moduleOptions->getLoginRedirectRoute());
        }

        $prg = $this->prg(static::ROUTE_CHANGEPASSWD);

        $fm = $this->flashMessenger()->setNamespace('change-password')->getMessages();
        if (isset($fm[0])) {
            $status = $fm[0];
        } else {
            $status = null;
        }

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return [
                'status' => $status,
                'changePasswordForm' => $this->changePasswordForm,
            ];
        }

        $this->changePasswordForm->setData($prg);

        if (! $this->changePasswordForm->isValid()) {
            return [
                'status' => false,
                'changePasswordForm' => $this->changePasswordForm,
            ];
        }

        if (! $this->userService->changePassword($this->changePasswordForm->getData())) {
            return [
                'status' => false,
                'changePasswordForm' => $this->changePasswordForm,
            ];
        }

        $this->flashMessenger()->setNamespace('change-password')->addMessage(true);
        return $this->redirect()->toRoute(static::ROUTE_CHANGEPASSWD);
    }

    public function changeEmailAction()
    {
        // if the user isn't logged in, we can't change email
        if (! $this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->moduleOptions->getLoginRedirectRoute());
        }

        $request = $this->getRequest();
        $request->getPost()->set(
            'identity',
            $this->zfcUserAuthentication()->getAuthService()->getIdentity()->getEmail()
        );

        $fm = $this->flashMessenger()->setNamespace('change-email')->getMessages();
        if (isset($fm[0])) {
            $status = $fm[0];
        } else {
            $status = null;
        }

        $prg = $this->prg(static::ROUTE_CHANGEEMAIL);
        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return [
                'status' => $status,
                'changeEmailForm' => $this->changeEmailForm,
            ];
        }

        $this->changeEmailForm->setData($prg);

        if (! $this->changeEmailForm->isValid()) {
            return [
                'status' => false,
                'changeEmailForm' => $this->changeEmailForm,
            ];
        }

        $change = $this->userService->changeEmail($prg);

        if (! $change) {
            $this->flashMessenger()->setNamespace('change-email')->addMessage(false);
            return [
                'status' => false,
                'changeEmailForm' => $this->changeEmailForm,
            ];
        }

        $this->flashMessenger()->setNamespace('change-email')->addMessage(true);
        return $this->redirect()->toRoute(static::ROUTE_CHANGEEMAIL);
    }
}
