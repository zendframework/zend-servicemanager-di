<?php
/**
 * @link      http://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager\Di;

use Exception;
use Interop\Container\ContainerInterface;
use Zend\Di\Di;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiServiceInitializer extends Di
{
    /**
     * @var ContainerInterface
     */
    protected $container = null;

    /**
     * @var Di
     */
    protected $di = null;

    /**
     * @var DiInstanceManagerProxy
     */
    protected $diInstanceManagerProxy = null;

    /**
     * @param Di $di
     * @param ContainerInterface $container
     * @param null|DiInstanceManagerProxy $diImProxy
     */
    public function __construct(
        Di $di,
        ContainerInterface $container,
        DiInstanceManagerProxy $diInstanceManagerProxy = null
    ) {
        $this->di = $di;
        $this->container = $container;
        $this->diInstanceManagerProxy = $diInstanceManagerProxy ?: new DiInstanceManagerProxy(
            $di->instanceManager(),
            $container
        );
    }

    /**
     * Initialize an instance via zend-di.
     *
     * @param mixed|ContainerInterface $first Container when under
     *     zend-servicemanager v3, instance to initialize otherwise.
     * @param ContainerInterface|mixed $second Instance to initialize when
     *     under zend-servicemanager v3, container otherwise.
     * @return void
     */
    public function __invoke($first, $second)
    {
        if ($first instanceof AbstractPluginManager
            || $second instanceof ContainerInterface
        ) {
            $instance  = $first;
        } else {
            $instance  = $second;
        }

        $instanceManager = $this->di->instanceManager;
        $this->di->instanceManager = $this->diInstanceManagerProxy;

        try {
            $this->di->injectDependencies($instance);
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->di->instanceManager = $instanceManager;
        }
    }
}
