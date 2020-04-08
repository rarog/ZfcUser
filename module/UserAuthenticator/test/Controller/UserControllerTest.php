<?php

namespace UserAuthenticatorTest\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Result;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\Mvc\Controller\Plugin\Forward;
use Laminas\Mvc\Controller\Plugin\Params;
use Laminas\Mvc\Controller\Plugin\Redirect;
use Laminas\Mvc\Controller\Plugin\Url;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Mvc\Plugin\Prg\PostRedirectGet;
use Laminas\Stdlib\Parameters;
use Laminas\View\Model\ViewModel;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Authentication\Adapter\AdapterChain;
use UserAuthenticator\Controller\RedirectCallback;
use UserAuthenticator\Controller\UserController;
use UserAuthenticator\Controller\Plugin\UserAuthenticatorAuthentication;
use UserAuthenticator\Form\ChangeEmail as ChangeEmailForm;
use UserAuthenticator\Form\ChangePassword as ChangePasswordForm;
use UserAuthenticator\Form\Login as LoginForm;
use UserAuthenticator\Form\Register as RegisterForm;
use UserAuthenticator\Model\User as UserModel;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Service\UserService;
use ReflectionProperty;
use stdClass;
use PHPUnit\Framework\MockObject\MockObject;

class UserControllerTest extends TestCase
{
    /**
     * @var UserController
     */
    private $userController;

    /**
     * @var MockObject
     */
    private $userService;

    /**
     * @var MockObject
     */
    private $changeEmailForm;

    /**
     * @var MockObject
     */
    private $changePasswordForm;

    /**
     * @var MockObject
     */
    private $loginForm;

    /**
     * @var MockObject
     */
    private $registerForm;

    /**
     * @var MockObject
     */
    private $moduleOptions;

    private $pluginManager;

    public $pluginManagerPlugins = [];

    protected $zfcUserAuthenticationPlugin;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|RedirectCallback
     */
    protected $redirectCallback;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        /**
         * @var MockObject
         */
        $this->redirectCallback = $this->getMockBuilder(RedirectCallback::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userService = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->changeEmailForm = $this->getMockBuilder(ChangeEmailForm::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->changePasswordForm = $this->getMockBuilder(ChangePasswordForm::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loginForm = $this->getMockBuilder(LoginForm::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registerForm = $this->getMockBuilder(RegisterForm::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->moduleOptions = $this->getMockBuilder(ModuleOptions::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userController  = new UserController(
            $this->redirectCallback,
            $this->userService,
            $this->changeEmailForm,
            $this->changePasswordForm,
            $this->loginForm,
            $this->registerForm,
            $this->moduleOptions
        );

        $this->zfcUserAuthenticationPlugin = $this->getMockBuilder(UserAuthenticatorAuthentication::class)
            ->getMock();

        $pluginManager = $this->getMockBuilder(PluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pluginManager->expects($this->any())
            ->method('get')
            ->will($this->returnCallback([$this, 'helperMockCallbackPluginManagerGet']));

        $this->pluginManager = $pluginManager;

        $this->userController->setPluginManager($pluginManager);
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->pluginManager);
        unset($this->zfcUserAuthenticationPlugin);
        unset($this->moduleOptions);
        unset($this->registerForm);
        unset($this->loginForm);
        unset($this->changePasswordForm);
        unset($this->changeEmailForm);
        unset($this->userService);
        unset($this->redirectCallback);
        unset($this->userController);
    }

    public function setUpZfcUserAuthenticationPlugin($option): UserAuthenticatorAuthentication
    {
        if (array_key_exists('hasIdentity', $option)) {
            $return = (is_callable($option['hasIdentity']))
                ? $this->returnCallback($option['hasIdentity'])
                : $this->returnValue($option['hasIdentity']);
            $this->zfcUserAuthenticationPlugin->expects($this->any())
                ->method('hasIdentity')
                ->will($return);
        }

        if (array_key_exists('getAuthAdapter', $option)) {
            $return = (is_callable($option['getAuthAdapter']))
                ? $this->returnCallback($option['getAuthAdapter'])
                : $this->returnValue($option['getAuthAdapter']);

            $this->zfcUserAuthenticationPlugin->expects($this->any())
                ->method('getAuthAdapter')
                ->will($return);
        }

        if (array_key_exists('getAuthService', $option)) {
            $return = (is_callable($option['getAuthService']))
                ? $this->returnCallback($option['getAuthService'])
                : $this->returnValue($option['getAuthService']);

            $this->zfcUserAuthenticationPlugin->expects($this->any())
                ->method('getAuthService')
                ->will($return);
        }

        $this->pluginManagerPlugins['zfcUserAuthentication'] = $this->zfcUserAuthenticationPlugin;

        return $this->zfcUserAuthenticationPlugin;
    }

    /**
     * @dataProvider providerTestActionControllHasIdentity
     */
    public function testActionControllHasIdentity($methodeName, $hasIdentity, $redirectRoute, $optionGetter): void
    {
        $controller = $this->userController;
        $redirectRoute = $redirectRoute ?: $controller::ROUTE_LOGIN;

        $this->setUpZfcUserAuthenticationPlugin([
            'hasIdentity' => $hasIdentity
        ]);

        $response = new Response();

        if ($optionGetter) {
            $this->moduleOptions->expects($this->once())
                ->method($optionGetter)
                ->will($this->returnValue($redirectRoute));
        }

        $redirect = $this->getMockBuilder(Redirect::class)
            ->getMock();
        $redirect->expects($this->once())
            ->method('toRoute')
            ->with($redirectRoute)
            ->will($this->returnValue($response));

        $this->pluginManagerPlugins['redirect'] = $redirect;

        $result = call_user_func([$controller, $methodeName]);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * @depend testActionControllHasIdentity
     */
    public function testIndexActionLoggedIn(): void
    {
        $controller = $this->userController;
        $this->setUpZfcUserAuthenticationPlugin([
            'hasIdentity' => true
        ]);

        $result = $controller->indexAction();

        $this->assertInstanceOf(ViewModel::class, $result);
    }


    /**
     * @dataProvider providerTrueOrFalseX2
     * @depend testActionControllHasIdentity
     */
    public function testLoginActionValidFormRedirectFalse($isValid, $wantRedirect): void
    {
        $controller = $this->userController;
        $redirectUrl = 'localhost/redirect1';

        $this->setUpZfcUserAuthenticationPlugin([
            'hasIdentity' => false
        ]);

        $flashMessenger = $this->getMockBuilder(FlashMessenger::class)
            ->getMock();
        $this->pluginManagerPlugins['flashMessenger'] = $flashMessenger;

        $flashMessenger->expects($this->any())
            ->method('setNamespace')
            ->with('zfcuser-login-form')
            ->will($this->returnSelf());

        $flashMessenger->expects($this->any())
            ->method('addMessage')
            ->will($this->returnSelf());

        $postArray = ['some', 'data'];
        $request = $this->getMockBuilder(Request::class)
            ->getMock();
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));
        $request->expects($this->any())
            ->method('getPost')
            ->will($this->returnValue($postArray));

        $this->helperMakePropertyAccessable($controller, 'request', $request);

        $this->loginForm->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue((bool) $isValid));

        $this->moduleOptions->expects($this->any())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue((bool) $wantRedirect));
        if ($wantRedirect) {
            $params = new Parameters();
            $params->set('redirect', $redirectUrl);

            $request->expects($this->any())
                ->method('getQuery')
                ->will($this->returnValue($params));
        }

        if ($isValid) {
            $adapter = $this->getMockBuilder(AdapterChain::class)
                ->getMock();
            $adapter->expects($this->once())
                ->method('resetAdapters');

            $service = $this->getMockBuilder(AuthenticationService::class)
                ->getMock();
            $service->expects($this->once())
                ->method('clearIdentity');

            $this->setUpZfcUserAuthenticationPlugin([
                'getAuthAdapter' => $adapter,
                'getAuthService' => $service
            ]);

            $this->loginForm->expects($this->once())
                ->method('setData')
                ->with($postArray);

            $expectedResult = new stdClass();

            $forwardPlugin = $this->getMockBuilder(Forward::class)
                ->disableOriginalConstructor()
                ->getMock();
            $forwardPlugin->expects($this->once())
                ->method('dispatch')
                ->with($controller::CONTROLLER_NAME, ['action' => 'authenticate'])
                ->will($this->returnValue($expectedResult));

            $this->pluginManagerPlugins['forward'] = $forwardPlugin;
        } else {
            $response = new Response();

            $redirectQuery = $wantRedirect ? '?redirect=' . rawurlencode($redirectUrl) : '';
            $route_url = '/user/login';


            $redirect = $this->getMockBuilder(Redirect::class, ['toUrl'])
                ->getMock();
            $redirect->expects($this->any())
                ->method('toUrl')
                ->with($route_url . $redirectQuery)
                ->will($this->returnCallback(function ($url) use (&$response) {
                    $response->getHeaders()->addHeaderLine('Location', $url);
                    $response->setStatusCode(302);

                    return $response;
                }));

            $this->pluginManagerPlugins['redirect'] = $redirect;

            $response = new Response();
            $url = $this->getMockBuilder(Url::class, ['fromRoute'])
                ->getMock();
            $url->expects($this->once())
                ->method('fromRoute')
                ->with($controller::ROUTE_LOGIN)
                ->will($this->returnValue($route_url));

            $this->pluginManagerPlugins['url'] = $url;
        }

        $result = $controller->loginAction();

        if ($isValid) {
            $this->assertSame($expectedResult, $result);
        } else {
            $this->assertInstanceOf(Response::class, $result);
            $this->assertEquals($response, $result);
            $this->assertEquals($route_url . $redirectQuery, $result->getHeaders()->get('Location')->getFieldValue());
        }
    }

    /**
     * @dataProvider providerTrueOrFalse
     * @depend testActionControllHasIdentity
     */
    public function testLoginActionIsNotPost($redirect): void
    {
        $this->setUpZfcUserAuthenticationPlugin([
            'hasIdentity' => false
        ]);

        $flashMessenger = $this->getMockBuilder(FlashMessenger::class)
            ->getMock();

        $this->pluginManagerPlugins['flashMessenger'] = $flashMessenger;

        $request = $this->getMockBuilder(Request::class)
            ->getMock();
        $request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(false));

        $this->loginForm->expects($this->never())
            ->method('isValid');

        $this->moduleOptions->expects($this->any())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue((bool) $redirect));
        if ($redirect) {
            $params = new Parameters();
            $params->set('redirect', 'http://localhost/');

            $request->expects($this->any())
                ->method('getQuery')
                ->will($this->returnValue($params));
        }

        $this->helperMakePropertyAccessable($this->userController, 'request', $request);

        $result = $this->userController->loginAction();

        $this->assertArrayHasKey('loginForm', $result);
        $this->assertArrayHasKey('redirect', $result);
        $this->assertArrayHasKey('enableRegistration', $result);

        $this->assertInstanceOf(LoginForm::class, $result['loginForm']);
        $this->assertSame($this->loginForm, $result['loginForm']);

        if ($redirect) {
            $this->assertEquals('http://localhost/', $result['redirect']);
        } else {
            $this->assertFalse($result['redirect']);
        }

        $this->assertEquals($this->moduleOptions->getEnableRegistration(), $result['enableRegistration']);
    }


    /**
     * @dataProvider providerRedirectPostQueryMatrix
     * @depend testActionControllHasIdentity
     */
    public function testLogoutAction($withRedirect, $post, $query): void
    {
        $controller = $this->userController;

        $adapter = $this->getMockBuilder(AdapterChain::class)
            ->getMock();
        $adapter->expects($this->once())
            ->method('resetAdapters');

        $adapter->expects($this->once())
            ->method('logoutAdapters');

        $service = $this->getMockBuilder(AuthenticationService::class)
            ->getMock();
        $service->expects($this->once())
            ->method('clearIdentity');

        $this->setUpZfcUserAuthenticationPlugin([
            'getAuthAdapter' => $adapter,
            'getAuthService' => $service
        ]);

        $response = new Response();

        $this->redirectCallback->expects($this->once())
            ->method('__invoke')
            ->will($this->returnValue($response));

        $result = $controller->logoutAction();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * @dataProvider providerTestAuthenticateAction
     * @depend testActionControllHasIdentity
     */
    public function testAuthenticateAction(
        $wantRedirect,
        $post,
        $query,
        $prepareResult = false,
        $authValid = false
    ): void {
        $controller = $this->userController;
        $response = new Response();

        $params = $this->getMockBuilder(Params::class)
            ->getMock();
        $params->expects($this->any())
            ->method('__invoke')
            ->will($this->returnSelf());
        $params->expects($this->once())
            ->method('fromPost')
            ->will($this->returnCallback(function ($key, $default) use ($post) {
                return $post ?: $default;
            }));
        $params->expects($this->once())
            ->method('fromQuery')
            ->will($this->returnCallback(function ($key, $default) use ($query) {
                return $query ?: $default;
            }));
        $this->pluginManagerPlugins['params'] = $params;

        $request = $this->getMockBuilder(Request::class)
            ->getMock();
        $this->helperMakePropertyAccessable($controller, 'request', $request);

        $adapter = $this->getMockBuilder(AdapterChain::class)
            ->getMock();
        $adapter->expects($this->once())
            ->method('prepareForAuthentication')
            ->with($request)
            ->will($this->returnValue($prepareResult));

        $service = $this->getMockBuilder(AuthenticationService::class)
            ->getMock();

        $this->setUpZfcUserAuthenticationPlugin([
            'hasIdentity' => false,
            'getAuthAdapter' => $adapter,
            'getAuthService' => $service
        ]);

        if (is_bool($prepareResult)) {
            $authResult = $this->getMockBuilder(Result::class)
                ->disableOriginalConstructor()
                ->getMock();
            $authResult->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue($authValid));

            $service->expects($this->once())
                ->method('authenticate')
                ->with($adapter)
                ->will($this->returnValue($authResult));

            $redirect = $this->getMockBuilder(Redirect::class)
                ->getMock();
            $this->pluginManagerPlugins['redirect'] = $redirect;

            if (! $authValid) {
                $flashMessenger = $this->getMockBuilder(FlashMessenger::class)
                    ->getMock();
                $this->pluginManagerPlugins['flashMessenger'] = $flashMessenger;

                $flashMessenger->expects($this->once())
                    ->method('setNamespace')
                    ->with('zfcuser-login-form')
                    ->will($this->returnSelf());

                $flashMessenger->expects($this->once())
                    ->method('addMessage');

                $adapter->expects($this->once())
                    ->method('resetAdapters');

                $redirectQuery = ($post ?: $query ?: false);
                $redirectQuery = $redirectQuery ? '?redirect=' . rawurlencode($redirectQuery) : '';

                $redirect->expects($this->once())
                    ->method('toUrl')
                    ->with('user/login' . $redirectQuery)
                    ->will($this->returnValue($response));

                $url = $this->getMockBuilder(Url::class)
                    ->getMock();
                $url->expects($this->once())
                    ->method('fromRoute')
                    ->with($controller::ROUTE_LOGIN)
                    ->will($this->returnValue('user/login'));
                $this->pluginManagerPlugins['url'] = $url;
            } else {
                $this->redirectCallback->expects($this->once())
                    ->method('__invoke');
            }

            $this->moduleOptions->expects($this->any())
                ->method('getUseRedirectParameterIfPresent')
                ->will($this->returnValue((bool) $wantRedirect));
        }

        $controller->authenticateAction();
    }

    /**
     *
     * @depend testActionControllHasIdentity
     */
    public function testRegisterActionIsNotAllowed(): void
    {
        $controller = $this->userController;

        $this->setUpZfcUserAuthenticationPlugin([
            'hasIdentity' => false
        ]);

        $this->moduleOptions->expects($this->once())
            ->method('getEnableRegistration')
            ->will($this->returnValue(false));

        $result = $controller->registerAction();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('enableRegistration', $result);
        $this->assertFalse($result['enableRegistration']);
    }

    /**
     *
     * @dataProvider providerTestRegisterAction
     * @depend testActionControllHasIdentity
     * @depend testRegisterActionIsNotAllowed
     */
    public function testRegisterAction(
        $wantRedirect,
        $postRedirectGetReturn,
        $registerSuccess,
        $loginAfterSuccessWith
    ): void {
        $controller = $this->userController;
        $redirectUrl = 'localhost/redirect1';
        $route_url = '/user/register';
        $expectedResult = null;

        $this->setUpZfcUserAuthenticationPlugin([
            'hasIdentity' => false
        ]);

        $this->moduleOptions->expects($this->any())
            ->method('getEnableRegistration')
            ->will($this->returnValue(true));

        $request = $this->getMockBuilder(Request::class)
            ->getMock();
        $this->helperMakePropertyAccessable($controller, 'request', $request);

        if ($registerSuccess) {
            $user = new UserModel();
            $user->setEmail('zfc-user@trash-mail.com');
            $user->setUsername('zfc-user');
        }

        if (! ($postRedirectGetReturn instanceof Response) && ($postRedirectGetReturn !== false)) {
            $this->moduleOptions->expects($this->any())
                ->method('getUserEntityClass')
                ->will($this->returnValue(UserModel::class));

            $this->registerForm->expects($this->once())
                ->method('setHydrator');
            $this->registerForm->expects($this->once())
                ->method('bind');
            $this->registerForm->expects($this->once())
                ->method('setData');
            $this->registerForm->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue($registerSuccess));

            if ($registerSuccess) {
                $this->registerForm->expects($this->once())
                    ->method('getData')
                    ->will($this->returnValue($user));
            }
        }

        $this->moduleOptions->expects($this->any())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue((bool) $wantRedirect));

        if ($wantRedirect) {
            $params = new Parameters();
            $params->set('redirect', $redirectUrl);

            $request->expects($this->any())
                ->method('getQuery')
                ->will($this->returnValue($params));
        }

        $url = $this->getMockBuilder(Url::class)
            ->getMock();
        $url->expects($this->at(0))
            ->method('fromRoute')
            ->with($controller::ROUTE_REGISTER)
            ->will($this->returnValue($route_url));

        $this->pluginManagerPlugins['url'] = $url;

        $prg = $this->getMockBuilder(PostRedirectGet::class)
            ->getMock();
        $this->pluginManagerPlugins['prg'] = $prg;

        $redirectQuery = $wantRedirect ? '?redirect=' . rawurlencode($redirectUrl) : '';
        $prg->expects($this->once())
            ->method('__invoke')
            ->with($route_url . $redirectQuery)
            ->will($this->returnValue($postRedirectGetReturn));

        if ($registerSuccess) {
            $this->userService->expects($this->once())
                ->method('register')
                ->with($user)
                ->will($this->returnValue($user));

            $this->moduleOptions->expects($this->once())
                ->method('getLoginAfterRegistration')
                ->will($this->returnValue(! empty($loginAfterSuccessWith)));

            if ($loginAfterSuccessWith) {
                $this->moduleOptions->expects($this->once())
                    ->method('getAuthIdentityFields')
                    ->will($this->returnValue([$loginAfterSuccessWith]));

                $expectedResult = new stdClass();
                $forwardPlugin = $this->getMockBuilder(Forward::class)
                    ->disableOriginalConstructor()
                    ->getMock();
                $forwardPlugin->expects($this->once())
                    ->method('dispatch')
                    ->with($controller::CONTROLLER_NAME, ['action' => 'authenticate'])
                    ->will($this->returnValue($expectedResult));

                $this->pluginManagerPlugins['forward'] = $forwardPlugin;
            } else {
                $response = new Response();
                $route_url = '/user/login';

                $redirectUrl = isset($postRedirectGetReturn['redirect'])
                    ? $postRedirectGetReturn['redirect']
                    : null;

                $redirectQuery = $redirectUrl ? '?redirect=' . rawurlencode($redirectUrl) : '';

                $redirect = $this->getMockBuilder(Redirect::class)
                    ->getMock();
                $redirect->expects($this->once())
                    ->method('toUrl')
                    ->with($route_url . $redirectQuery)
                    ->will($this->returnValue($response));

                $this->pluginManagerPlugins['redirect'] = $redirect;

                $url->expects($this->at(1))
                    ->method('fromRoute')
                    ->with($controller::ROUTE_LOGIN)
                    ->will($this->returnValue($route_url));
            }
        }

        /***********************************************
         * run
         */
        $result = $controller->registerAction();

        /***********************************************
         * assert
         */
        if ($postRedirectGetReturn instanceof Response) {
            $expectedResult = $postRedirectGetReturn;
        }
        if ($expectedResult) {
            $this->assertSame($expectedResult, $result);
            return;
        }

        if ($postRedirectGetReturn === false) {
            $expectedResult = [
                'registerForm' => $this->registerForm,
                'enableRegistration' => true,
                'redirect' => $wantRedirect ? $redirectUrl : false
            ];
        } elseif ($registerSuccess === false) {
            $expectedResult = [
                'registerForm' => $this->registerForm,
                'enableRegistration' => true,
                'redirect' => isset($postRedirectGetReturn['redirect']) ? $postRedirectGetReturn['redirect'] : null
            ];
        }

        if ($expectedResult) {
            $this->assertIsArray($result);
            $this->assertArrayHasKey('registerForm', $result);
            $this->assertArrayHasKey('enableRegistration', $result);
            $this->assertArrayHasKey('redirect', $result);
            $this->assertEquals($expectedResult, $result);
        } else {
            $this->assertInstanceOf(Response::class, $result);
            $this->assertSame($response, $result);
        }
    }


    /**
     * @dataProvider providerTestChangeAction
     * @depend testActionControllHasIdentity
     */
    public function testChangepasswordAction($status, $postRedirectGetReturn, $isValid, $changeSuccess): void
    {
        $controller = $this->userController;
        $response = new Response();

        $this->setUpZfcUserAuthenticationPlugin([
            'hasIdentity' => true
        ]);

        $flashMessenger = $this->getMockBuilder(FlashMessenger::class)
            ->getMock();
        $this->pluginManagerPlugins['flashMessenger'] = $flashMessenger;

        $flashMessenger->expects($this->any())
            ->method('setNamespace')
            ->with('change-password')
            ->will($this->returnSelf());

        $flashMessenger->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue($status ? ['test'] : []));

        $prg = $this->getMockBuilder(PostRedirectGet::class)
            ->getMock();
        $this->pluginManagerPlugins['prg'] = $prg;

        $prg->expects($this->once())
            ->method('__invoke')
            ->with($controller::ROUTE_CHANGEPASSWD)
            ->will($this->returnValue($postRedirectGetReturn));

        if ($postRedirectGetReturn !== false && ! ($postRedirectGetReturn instanceof Response)) {
            $this->changePasswordForm->expects($this->once())
                ->method('setData')
                ->with($postRedirectGetReturn);

            $this->changePasswordForm->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue((bool) $isValid));

            if ($isValid) {
                $this->changePasswordForm->expects($this->once())
                    ->method('getData')
                    ->will($this->returnValue($postRedirectGetReturn));

                $this->userService->expects($this->once())
                    ->method('changePassword')
                    ->with($postRedirectGetReturn)
                    ->will($this->returnValue((bool) $changeSuccess));


                if ($changeSuccess) {
                    $flashMessenger->expects($this->once())
                        ->method('addMessage')
                        ->with(true);

                    $redirect = $this->getMockBuilder(Redirect::class)
                        ->getMock();
                    $redirect->expects($this->once())
                        ->method('toRoute')
                        ->with($controller::ROUTE_CHANGEPASSWD)
                        ->will($this->returnValue($response));

                    $this->pluginManagerPlugins['redirect'] = $redirect;
                }
            }
        }


        $result = $controller->changepasswordAction();
        $exceptedReturn = null;

        if ($postRedirectGetReturn instanceof Response) {
            $this->assertInstanceOf(Response::class, $result);
            $this->assertSame($postRedirectGetReturn, $result);
        } else {
            if ($postRedirectGetReturn === false) {
                $exceptedReturn = [
                    'status' => $status ? 'test' : null,
                    'changePasswordForm' => $this->changePasswordForm,
                ];
            } elseif ($isValid === false || $changeSuccess === false) {
                $exceptedReturn = [
                    'status' => false,
                    'changePasswordForm' => $this->changePasswordForm,
                ];
            }
            if ($exceptedReturn) {
                $this->assertIsArray($result);
                $this->assertArrayHasKey('status', $result);
                $this->assertArrayHasKey('changePasswordForm', $result);
                $this->assertEquals($exceptedReturn, $result);
            } else {
                $this->assertInstanceOf(Response::class, $result);
                $this->assertSame($response, $result);
            }
        }
    }


    /**
     * @dataProvider providerTestChangeAction
     * @depend testActionControllHasIdentity
     */
    public function testChangeEmailAction($status, $postRedirectGetReturn, $isValid, $changeSuccess): void
    {
        $controller = $this->userController;
        $response = new Response();
        $authService = $this->getMockBuilder(AuthenticationService::class)
            ->getMock();
        $identity = new UserModel();

        $this->setUpZfcUserAuthenticationPlugin([
            'hasIdentity' => true,
            'getAuthService' => $authService
        ]);

        $controller->setChangeEmailForm($this->changeEmailForm);

        $authService->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($identity));
        $identity->setEmail('user@example.com');


        $requestParams = $this->getMockBuilder(Parameters::class)
            ->getMock();
        $requestParams->expects($this->once())
            ->method('set')
            ->with('identity', $identity->getEmail());

        $request = $this->getMockBuilder(Request::class)
            ->getMock();
        $request->expects($this->once())
            ->method('getPost')
            ->will($this->returnValue($requestParams));
        $this->helperMakePropertyAccessable($controller, 'request', $request);

        $flashMessenger = $this->getMockBuilder(FlashMessenger::class)
            ->getMock();
        $this->pluginManagerPlugins['flashMessenger'] = $flashMessenger;

        $flashMessenger->expects($this->any())
            ->method('setNamespace')
            ->with('change-email')
            ->will($this->returnSelf());

        $flashMessenger->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue($status ? ['test'] : []));

        $prg = $this->getMockBuilder(PostRedirectGet::class)
            ->getMock();
        $this->pluginManagerPlugins['prg'] = $prg;

        $prg->expects($this->once())
            ->method('__invoke')
            ->with($controller::ROUTE_CHANGEEMAIL)
            ->will($this->returnValue($postRedirectGetReturn));

        if ($postRedirectGetReturn !== false && ! ($postRedirectGetReturn instanceof Response)) {
            $this->changeEmailForm->expects($this->once())
                ->method('setData')
                ->with($postRedirectGetReturn);

            $this->changeEmailForm->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue((bool) $isValid));

            if ($isValid) {
                $this->userService->expects($this->once())
                    ->method('changeEmail')
                    ->with($postRedirectGetReturn)
                    ->will($this->returnValue((bool) $changeSuccess));

                if ($changeSuccess) {
                    $flashMessenger->expects($this->once())
                        ->method('addMessage')
                        ->with(true);

                    $redirect = $this->getMockBuilder(Redirect::class)
                        ->getMock();
                    $redirect->expects($this->once())
                        ->method('toRoute')
                        ->with($controller::ROUTE_CHANGEEMAIL)
                        ->will($this->returnValue($response));

                    $this->pluginManagerPlugins['redirect'] = $redirect;
                } else {
                    $flashMessenger->expects($this->once())
                        ->method('addMessage')
                        ->with(false);
                }
            }
        }

        $result = $controller->changeEmailAction();
        $exceptedReturn = null;

        if ($postRedirectGetReturn instanceof Response) {
            $this->assertInstanceOf(Response::class, $result);
            $this->assertSame($postRedirectGetReturn, $result);
        } else {
            if ($postRedirectGetReturn === false) {
                $exceptedReturn = [
                    'status' => $status ? 'test' : null,
                    'changeEmailForm' => $this->changeEmailForm,
                ];
            } elseif ($isValid === false || $changeSuccess === false) {
                $exceptedReturn = [
                    'status' => false,
                    'changeEmailForm' => $this->changeEmailForm,
                ];
            }

            if ($exceptedReturn) {
                $this->assertIsArray($result);
                $this->assertArrayHasKey('status', $result);
                $this->assertArrayHasKey('changeEmailForm', $result);
                $this->assertEquals($exceptedReturn, $result);
            } else {
                $this->assertInstanceOf(Response::class, $result);
                $this->assertSame($response, $result);
            }
        }
    }

    public function providerTrueOrFalse(): array
    {
        return [
            [true],
            [false],
        ];
    }

    public function providerTrueOrFalseX2(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    public function providerTestAuthenticateAction(): array
    {
        // $redirect, $post, $query, $prepareResult = false, $authValid = false
        return [
            [false, null, null, new Response(), false],
            [false, null, null, false, false],
            [false, null, null, false, true],
            [false, 'localhost/test1', null, false, false],
            [false, 'localhost/test1', null, false, true],
            [false, 'localhost/test1', 'localhost/test2', false, false],
            [false, 'localhost/test1', 'localhost/test2', false, true],
            [false, null, 'localhost/test2', false, false],
            [false, null, 'localhost/test2', false, true],
            [true, null, null, false, false],
            [true, null, null, false, true],
            [true, 'localhost/test1', null, false, false],
            [true, 'localhost/test1', null, false, true],
            [true, 'localhost/test1', 'localhost/test2', false, false],
            [true, 'localhost/test1', 'localhost/test2', false, true],
            [true, null, 'localhost/test2', false, false],
            [true, null, 'localhost/test2', false, true],
        ];
    }

    public function providerRedirectPostQueryMatrix(): array
    {
        return [
            [false, false, false],
            [true, false, false],
            [true, 'localhost/test1', false],
            [true, 'localhost/test1', 'localhost/test2'],
            [true, false, 'localhost/test2'],
        ];
    }


    public function providerTestActionControllHasIdentity(): array
    {
        return [
            // $methodeName , $hasIdentity, $redirectRoute, optionsGetterMethode
            ['indexAction', false, UserController::ROUTE_LOGIN, null],
            ['loginAction', true, 'user/overview', 'getLoginRedirectRoute'],
            ['authenticateAction', true, 'user/overview', 'getLoginRedirectRoute'],
            ['registerAction', true, 'user/overview', 'getLoginRedirectRoute'],
            ['changepasswordAction', false, 'user/overview',  'getLoginRedirectRoute'],
            ['changeEmailAction', false, 'user/overview', 'getLoginRedirectRoute']

        ];
    }

    public function providerTestChangeAction(): array
    {
        return [
            // $status, $postRedirectGetReturn, $isValid, $changeSuccess
            [false, new Response(), null, null],
            [true, new Response(), null, null],
            [false, false, null, null],
            [true, false, null, null],
            [false, ['test'], false, null],
            [true, ['test'], false, null],
            [false, ['test'], true, false],
            [true, ['test'], true, false],
            [false, ['test'], true, true],
            [true, ['test'], true, true],
        ];
    }

    public function providerTestRegisterAction(): array
    {
        $registerPost = [
            'username' => 'zfc-user',
            'email' => 'zfc-user@trash-mail.com',
            'password' => 'secret'
        ];
        $registerPostRedirect = array_merge($registerPost, ['redirect' => 'test']);


        return [
            // $status, $postRedirectGetReturn, $registerSuccess, $loginAfterSuccessWith
            [false, new Response(), null, null],
            [true, new Response(), null, null],
            [false, false, null, null],
            [true, false, null, null],
            [false, $registerPost, false, null],
            [true, $registerPost, false, null],
            [false, $registerPostRedirect, false, null],
            [true, $registerPostRedirect, false, null],
            [false, $registerPost, true, 'email'],
            [true, $registerPost, true, 'email'],
            [false, $registerPostRedirect, true, 'email'],
            [true, $registerPostRedirect, true, 'email'],
            [false, $registerPost, true, 'username'],
            [true, $registerPost, true, 'username'],
            [false, $registerPostRedirect, true, 'username'],
            [true, $registerPostRedirect, true, 'username'],
            [false, $registerPost, true, null],
            [true, $registerPost, true, null],
            [false, $registerPostRedirect, true, null],
            [true, $registerPostRedirect, true, null],

        ];
    }

    /**
     *
     * @param mixed $objectOrClass
     * @param string $property
     * @param mixed $value = null
     * @return \ReflectionProperty
     */
    public function helperMakePropertyAccessable($objectOrClass, $property, $value = null): ReflectionProperty
    {
        $reflectionProperty = new ReflectionProperty($objectOrClass, $property);
        $reflectionProperty->setAccessible(true);

        if ($value !== null) {
            $reflectionProperty->setValue($objectOrClass, $value);
        }
        return $reflectionProperty;
    }

    public function helperMockCallbackPluginManagerGet($key)
    {
        return (array_key_exists($key, $this->pluginManagerPlugins))
            ? $this->pluginManagerPlugins[$key]
            : null;
    }
}
