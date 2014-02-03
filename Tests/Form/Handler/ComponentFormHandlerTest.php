<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Form\Handler;

use IC\Bundle\Base\ComponentBundle\Form\Handler\ComponentFormHandler;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Test ComponentFormHandler
 *
 * @group Unit
 * @group Form
 * @group FormHandler
 *
 * @author Anthon Pang <anthonp@nationalfibre.net>
 */
class ComponentFormHandlerTest extends TestCase
{
    /**
     * Test process() handling of disallowed request methods
     *
     * @param array $method
     *
     * @dataProvider provideDisallowedMethod
     */
    public function testProcessDisallowedMethod($method)
    {
        $form    = $this->prepareForm();
        $model   = new \stdClass;
        $service = new \stdClass;
        $handler = $this->prepareHandler($form, $method, $service);

        $this->assertTrue(null === $handler->process($model));
    }

    /**
     * Provide disallowed method
     *
     * @return array
     */
    public function provideDisallowedMethod()
    {
        return array(
            array('GET'),
            array('PUT'),
            array('DELETE'),
            array('HEAD'),
        );
    }

    /**
     * Test process() handling of invalid form
     */
    public function testProcessInvalidForm()
    {
        $form = $this->prepareForm();

        $form->expects($this->once())
             ->method('isSubmitted')
             ->will($this->returnValue(false));

        $form->expects($this->never())
             ->method('isValid');

        $model   = new \stdClass;
        $service = new \stdClass;
        $handler = $this->prepareHandler($form, 'POST', $service);

        $this->assertTrue(false === $handler->process($model));
    }

    /**
     * Test process() executes service
     */
    public function testProcess()
    {
        $form = $this->prepareForm();

        $form->expects($this->once())
             ->method('isSubmitted')
             ->will($this->returnValue(true));

        $form->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $service = $this->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Form\Service\Service');
        $service->expects($this->once())
                ->method('execute')
                ->will($this->returnValue(true));

        $model   = new \stdClass;
        $handler = $this->prepareHandler($form, 'POST', $service);

        $this->assertTrue(true === $handler->process($model));
    }

    /**
     * Test getForm()
     */
    public function testGetForm()
    {
        $form    = $this->prepareForm();
        $service = new \stdClass;
        $handler = $this->prepareHandler($form, 'POST', $service);

        $this->assertTrue($form === $handler->getForm());
    }

    private function prepareForm()
    {
        $form = $this->createMock('Symfony\Component\Form\Form');

        return $form;
    }

    private function prepareRequest($method)
    {
        $request = $this->createMock('Symfony\Component\HttpFoundation\Request');
        $request->expects($this->any())
                ->method('getMethod')
                ->will($this->returnValue($method));

        return $request;
    }

    private function prepareHandler($form, $requestMethod, $service)
    {
        $request = $this->prepareRequest($requestMethod);
        $handler = new ComponentFormHandler($form, $request, $service);

        return $handler;
    }
}
