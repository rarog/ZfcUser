<?php

namespace UserAuthenticatorTest\Controller;

use Laminas\Http\Headers;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteInterface;
use Laminas\Router\RouteMatch;
use Laminas\Router\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Controller\RedirectCallback;
use UserAuthenticator\Options\ModuleOptions;
use ReflectionMethod;

class RedirectCallbackTest extends TestCase
{

    /** @var RedirectCallback */
    protected $redirectCallback;

    /** @var \PHPUnit\Framework\MockObject\MockObject|ModuleOptions */
    protected $moduleOptions;

    /** @var  \PHPUnit\Framework\MockObject\MockObject|RouteInterface */
    protected $router;

    /** @var  \PHPUnit\Framework\MockObject\MockObject|Application */
    protected $application;

    /** @var  \PHPUnit\Framework\MockObject\MockObject|Request */
    protected $request;

    /** @var  \PHPUnit\Framework\MockObject\MockObject|Response */
    protected $response;

    /** @var  \PHPUnit\Framework\MockObject\MockObject|MvcEvent */
    protected $mvcEvent;

    /** @var  \PHPUnit\Framework\MockObject\MockObject|RouteMatch */
    protected $routeMatch;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->router = $this->getMockBuilder(RouteInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->moduleOptions = $this->getMockBuilder(ModuleOptions::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->application = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->setUpApplication();

        $this->redirectCallback = new RedirectCallback(
            $this->application,
            $this->router,
            $this->moduleOptions
        );
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->redirectCallback);
        unset($this->application);
        unset($this->moduleOptions);
        unset($this->router);
    }

    public function testInvoke()
    {
        $url = 'someUrl';

        $this->routeMatch->expects($this->once())
            ->method('getMatchedRouteName')
            ->will($this->returnValue('someRoute'));

        $headers = $this->getMockBuilder(Headers::class)
            ->getMock();
        $headers->expects($this->once())
            ->method('addHeaderLine')
            ->with('Location', $url);

        $this->router->expects($this->any())
            ->method('assemble')
            ->with([], ['name' => 'zfcuser'])
            ->will($this->returnValue($url));

        $this->response->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue($headers));

        $this->response->expects($this->once())
            ->method('setStatusCode')
            ->with(302);

        $result = $this->redirectCallback->__invoke();

        $this->assertSame($this->response, $result);
    }

    /**
     * @dataProvider providerGetRedirectRouteFromRequest
     */
    public function testGetRedirectRouteFromRequest($get, $post, $getRouteExists, $postRouteExists)
    {
        $expectedResult = false;

        $this->request->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($get));

        if ($get) {
            $this->router->expects($this->any())
                ->method('assemble')
                ->with([], ['name' => $get])
                ->will($getRouteExists);

            if ($getRouteExists == $this->returnValue(true)) {
                $expectedResult = $get;
            }
        }

        if (! $get || ! $getRouteExists) {
            $this->request->expects($this->once())
                ->method('getPost')
                ->will($this->returnValue($post));

            if ($post) {
                $this->router->expects($this->any())
                    ->method('assemble')
                    ->with([], ['name' => $post])
                    ->will($postRouteExists);

                if ($postRouteExists == $this->returnValue(true)) {
                    $expectedResult = $post;
                }
            }
        }

        $method = new ReflectionMethod(
            RedirectCallback::class,
            'getRedirectRouteFromRequest'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback);

        $this->assertSame($expectedResult, $result);
    }

    public function providerGetRedirectRouteFromRequest()
    {
        return [
            ['user', false, $this->returnValue('route'), false],
            ['user', false, $this->returnValue('route'), $this->returnValue(true)],
            ['user', 'user', $this->returnValue('route'), $this->returnValue(true)],
            ['user', 'user', $this->throwException(new RuntimeException()), $this->returnValue(true)],
            [
                'user',
                'user',
                $this->throwException(new RuntimeException()),
                $this->throwException(new RuntimeException())
            ],
            [false, 'user', false, $this->returnValue(true)],
            [false, 'user', false, $this->throwException(new RuntimeException())],
            [false, 'user', false, $this->throwException(new RuntimeException())],
        ];
    }

    public function testRouteExistsRouteExists()
    {
        $route = 'existingRoute';

        $this->router->expects($this->once())
            ->method('assemble')
            ->with([], ['name' => $route]);

        $method = new ReflectionMethod(
            RedirectCallback::class,
            'routeExists'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route);

        $this->assertTrue($result);
    }

    public function testRouteExistsRouteDoesntExists()
    {
        $route = 'existingRoute';

        $this->router->expects($this->once())
            ->method('assemble')
            ->with([], ['name' => $route])
            ->will($this->throwException(new RuntimeException()));

        $method = new ReflectionMethod(
            RedirectCallback::class,
            'routeExists'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider providerGetRedirectNoRedirectParam
     */
    public function testGetRedirectNoRedirectParam($currentRoute, $optionsReturn, $expectedResult, $optionsMethod)
    {
        $this->moduleOptions->expects($this->once())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue(true));

        $this->router->expects($this->at(0))
            ->method('assemble');
        $this->router->expects($this->at(1))
            ->method('assemble')
            ->with([], ['name' => $optionsReturn])
            ->will($this->returnValue($expectedResult));

        if ($optionsMethod) {
            $this->moduleOptions->expects($this->never())
                ->method($optionsMethod)
                ->will($this->returnValue($optionsReturn));
        }
        $method = new ReflectionMethod(
            RedirectCallback::class,
            'getRedirect'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $currentRoute, $optionsReturn);

        $this->assertSame($expectedResult, $result);
    }

    public function providerGetRedirectNoRedirectParam()
    {
        return [
            ['zfcuser/login', 'zfcuser', '/user', 'getLoginRedirectRoute'],
            ['zfcuser/authenticate', 'zfcuser', '/user', 'getLoginRedirectRoute'],
            ['zfcuser/logout', 'zfcuser/login', '/user/login', 'getLogoutRedirectRoute'],
            ['testDefault', 'zfcuser', '/home', false],
        ];
    }

    public function testGetRedirectWithOptionOnButNoRedirect()
    {
        $route = 'zfcuser/login';
        $redirect = false;
        $expectedResult = '/user/login';

        $this->moduleOptions->expects($this->once())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue(true));

        $this->moduleOptions->expects($this->once())
            ->method('getLoginRedirectRoute')
            ->will($this->returnValue($route));

        $this->router->expects($this->once())
            ->method('assemble')
            ->with([], ['name' => $route])
            ->will($this->returnValue($expectedResult));

        $method = new ReflectionMethod(
            RedirectCallback::class,
            'getRedirect'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route, $redirect);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetRedirectWithOptionOnRedirectDoesntExists()
    {
        $route = 'zfcuser/login';
        $redirect = 'doesntExists';
        $expectedResult = '/user/login';

        $this->moduleOptions->expects($this->once())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue(true));

        $this->router->expects($this->at(0))
            ->method('assemble')
            ->with([], ['name' => $redirect])
            ->will($this->throwException(new RuntimeException()));

        $this->router->expects($this->at(1))
            ->method('assemble')
            ->with([], ['name' => $route])
            ->will($this->returnValue($expectedResult));

        $this->moduleOptions->expects($this->once())
            ->method('getLoginRedirectRoute')
            ->will($this->returnValue($route));

        $method = new ReflectionMethod(
            RedirectCallback::class,
            'getRedirect'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route, $redirect);

        $this->assertSame($expectedResult, $result);
    }

    private function setUpApplication()
    {
        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->routeMatch = $this->getMockBuilder(RouteMatch::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mvcEvent = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mvcEvent->expects($this->any())
            ->method('getRouteMatch')
            ->will($this->returnValue($this->routeMatch));


        $this->application->expects($this->any())
            ->method('getMvcEvent')
            ->will($this->returnValue($this->mvcEvent));
        $this->application->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->application->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($this->response));
    }
}
