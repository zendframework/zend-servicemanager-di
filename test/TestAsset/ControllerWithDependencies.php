<?php
/**
 * @see       https://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-servicemanager-di/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\ServiceManager\Di\TestAsset;

use stdClass;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class ControllerWithDependencies implements DispatchableInterface
{
    /**
     * @var stdClass
     */
    public $injectedValue;

    /**
     * @param stdClass $injected
     */
    public function setInjectedValue(stdClass $injected)
    {
        $this->injectedValue = $injected;
    }

    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
    }
}
