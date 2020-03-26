<?php

namespace ZfcUserTest\View\Helper;

use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\RendererInterface;
use PHPUnit\Framework\TestCase;
use ZfcUser\Form\Login;
use ZfcUser\View\Helper\ZfcUserLoginWidget;
use ZfcUser\View\Helper\ZfcUserLoginWidget as ViewHelper;
use ReflectionClass;

class ZfcUserLoginWidgetTest extends TestCase
{
    protected $helper;

    protected $view;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->helper = new ViewHelper();

        $view = $this->getMockBuilder(RendererInterface::class)
            ->getMock();
        $this->view = $view;

        $this->helper->setView($view);
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->view);
        unset($this->helper);
    }

    public function providerTestInvokeWithRender(): array
    {
        $attr = [];
        $attr[] = [
            [
                'render' => true,
                'redirect' => 'zfcUser'
            ],
            [
                'loginForm' => null,
                'redirect' => 'zfcUser'
            ],
        ];
        $attr[] = [
            [
                'redirect' => 'zfcUser'
            ],
            [
                'loginForm' => null,
                'redirect' => 'zfcUser'
            ],
        ];
        $attr[] = [
            [
                'render' => true,
            ],
            [
                'loginForm' => null,
                'redirect' => false
            ],
        ];

        return $attr;
    }

    /**
     * @covers ZfcUser\View\Helper\ZfcUserLoginWidget::__invoke
     * @dataProvider providerTestInvokeWithRender
     */
    public function testInvokeWithRender($option, $expect): void
    {
        /**
         * @var $viewModel \Laminas\View\Model\ViewModel
         */
        $viewModel = null;

        $this->view->expects($this->at(0))
             ->method('render')
             ->will($this->returnCallback(function ($vm) use (&$viewModel) {
                 $viewModel = $vm;
                 return 'test';
             }));

        $result = $this->helper->__invoke($option);

        $this->assertNotInstanceOf(ViewModel::class, $result);
        $this->assertIsString($result);


        $this->assertInstanceOf(ViewModel::class, $viewModel);
        foreach ($expect as $name => $value) {
            $this->assertEquals($value, $viewModel->getVariable($name, 'testDefault'));
        }
    }

    /**
     * @covers ZfcUser\View\Helper\ZfcUserLoginWidget::__invoke
     */
    public function testInvokeWithoutRender(): void
    {
        $result = $this->helper->__invoke([
            'render' => false,
            'redirect' => 'zfcUser'
        ]);

        $this->assertInstanceOf(ViewModel::class, $result);
        $this->assertEquals('zfcUser', $result->redirect);
    }

    /**
     * @covers ZfcUser\View\Helper\ZfcUserLoginWidget::setLoginForm
     * @covers ZfcUser\View\Helper\ZfcUserLoginWidget::getLoginForm
     */
    public function testSetGetLoginForm(): void
    {
        $loginForm = $this->getMockBuilder(Login::class)->disableOriginalConstructor()->getMock();

        $this->helper->setLoginForm($loginForm);
        $this->assertInstanceOf(Login::class, $this->helper->getLoginForm());
    }

    /**
     * @covers ZfcUser\View\Helper\ZfcUserLoginWidget::setViewTemplate
     */
    public function testSetViewTemplate(): void
    {
        $this->helper->setViewTemplate('zfcUser');

        $reflectionClass = new ReflectionClass(ZfcUserLoginWidget::class);
        $reflectionProperty = $reflectionClass->getProperty('viewTemplate');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals('zfcUser', $reflectionProperty->getValue($this->helper));
    }
}
