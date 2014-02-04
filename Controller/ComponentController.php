<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Component Controller
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 * @author Oleksii Strutsynskyi <oleksiis@nationalfibre.net>
 * @author Yuan Xie <shayx@nationalfibre.net>
 */
abstract class ComponentController extends Controller
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\Form\Handler\ComponentFormHandler
     */
    private $componentFormHandler;

    /**
     * Process Component submission
     *
     * @param object $componentFormModel
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function process($componentFormModel)
    {
        $componentFormHandler = $this->getComponentFormHandler();
        $componentResponse    = $componentFormHandler->process($componentFormModel);

        return $this->handleResponse($componentResponse);
    }

    /**
     * Handle Component submission response
     *
     * @param mixed $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleResponse($response)
    {
        $requestService = $this->container->get('request');

        if ($requestService->isXmlHttpRequest()) {
            return $this->handleAjaxResponse($response);
        }

        return $this->handleNormalResponse($response);
    }

    /**
     * Handle Component AJAX submission response
     *
     * @param mixed $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleAjaxResponse($response)
    {
        $serializerService = $this->container->get('serializer');

        $componentFormHandler = $this->getComponentFormHandler();
        $componentForm        = $componentFormHandler->getForm();

        $headerList = array('Content-type' => 'application/json');

        // @todo turn this into an exception handling
        if ($response === false) {
            $errorList = $this->getComponentFormValidationErrors($componentForm);

            $body = array(
                'response' => array(
                    'type'   => 'error',
                    'data'   => array(
                        'formName'  => $componentForm->getName(),
                        'errorList' => $errorList
                    )
                )
            );

            return new Response($serializerService->serialize($body, 'json'), 400, $headerList);
        }

        $body = array(
            'response' => array(
                'type' => 'success',
                'data' => $response
            )
        );

        return new Response($serializerService->serialize($body, 'json'), 200, $headerList);
    }

    /**
     * Handle Component normal submission response
     *
     * @param mixed $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleNormalResponse($response)
    {
        $sessionService = $this->container->get('session');
        $requestService = $this->container->get('request');

        if ( ! $response) {
            $sessionService->set($this->getComponentName(), $requestService->request);

            return $this->redirect($requestService->get('redirect'));
        }

        return $this->redirect($this->generateUrl($this->getSuccessRoute()));
    }

    /**
     * Handle sub-request redirection
     *
     * @param string $url
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleSubRequest($url)
    {
        $httpKernelService = $this->container->get('http_kernel');
        $requestService    = $this->container->get('request');

        $request = Request::create(
            $url,
            'GET',
            array(),
            $requestService->cookies->all(),
            array(),
            $requestService->server->all()
        );

        return $httpKernelService->handle($request, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Retrieve the Component Form validation errors
     *
     * @param \Symfony\Component\Form\Form $componentForm
     *
     * @return array
     */
    protected function getComponentFormValidationErrors($componentForm)
    {
        $translatorService = $this->container->get('translator');
        $errorList         = array();

        foreach ($componentForm->getErrors() as $key => $error) {
            if ( ! is_null($error->getMessagePluralization())) {
                $errorList[$key] = $translatorService->transChoice(
                    $error->getMessageTemplate(),
                    $error->getMessagePluralization(),
                    $error->getMessageParameters(),
                    'validators'
                );

                continue;
            }

            $errorList[$key] = $translatorService->trans(
                $error->getMessageTemplate(),
                $error->getMessageParameters(),
                'validators'
            );
        }

        if (count($componentForm) === 0) {
            return $errorList;
        }

        $formChildren = array_filter(
            $componentForm->all(),
            function ($child) {
                return ! $child->isValid();
            }
        );

        foreach ($formChildren as $child) {
            $errorList[$child->getName()] = $this->getComponentFormValidationErrors($child);
        }

        return $errorList;
    }

    /**
     * Retrieve associated Component Form View
     *
     * @param object  $componentFormModel Component form model
     * @param integer $componentSuffix    Unique suffix when multiple forms with the same name are present
     *
     * @return \Symfony\Component\Form\FormView
     */
    protected function getComponentFormView($componentFormModel, $componentSuffix = null)
    {
        $sessionService = $this->container->get('session');

        $componentName        = $this->getComponentName() . $componentSuffix;
        $componentFormHandler = $this->getComponentFormHandler();
        $componentForm        = $componentFormHandler->getForm();

        if ( ! $componentForm->isBound()) {
            $componentForm->setData($componentFormModel);
        }

        // If it is a normal HTTP request, but contains errors,
        // it was submitted using normal flow
        if ($sessionService->has($componentName)) {
            // Retrieve previous request data
            $componentErrorList = $sessionService->get($componentName);
            $clientData         = $componentErrorList->get($componentForm->getName());

            $sessionService->remove($componentName);

            $componentForm->submit($clientData);
        }

        return $componentForm->createView();
    }

    /**
     * Retrieve associated Component Form Handler
     *
     * @return \IC\Bundle\Base\ComponentBundle\Form\Handler\ComponentFormHandler
     */
    protected function getComponentFormHandler()
    {
        if ( ! $this->componentFormHandler) {
            $this->componentFormHandler = $this->container->get($this->getComponentName());
        }

        return $this->componentFormHandler;
    }

    /**
     * Retrieve component name
     *
     * @abstract
     *
     * @return string
     */
    abstract protected function getComponentName();

    /**
     * Retrieve the Component Form submission success redirection route.
     *
     * @abstract
     *
     * @return string
     */
    abstract protected function getSuccessRoute();
}
