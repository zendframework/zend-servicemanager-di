<?php
/**
 * @see       https://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-servicemanager-di/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ServiceManager\Di;

use Interop\Container\ContainerInterface;
use Zend\Di\InstanceManager as DiInstanceManager;

/**
 * Proxy for the Zend\Di\InstanceManager.
 *
 * Allows testing against either the composed container or the composed
 * InstanceManager for purposes of determining if an instance is available
 * and/or returning the instance.
 *
 * This class is consumed by the DiServiceInitializer within its internals.
 */
class DiInstanceManagerProxy extends DiInstanceManager
{
    /**
     * @var ContainerInterface
     */
    protected $container = null;

    /**
     * @var DiInstanceManager
     */
    protected $diInstanceManager = null;

    /**
     * @param DiInstanceManager $diInstanceManager
     * @param ContainerInterface $container
     */
    public function __construct(DiInstanceManager $diInstanceManager, ContainerInterface $container)
    {
        $this->diInstanceManager = $diInstanceManager;
        $this->container = $container;

        // localize state
        $this->aliases = &$diInstanceManager->aliases;
        $this->sharedInstances = &$diInstanceManager->sharedInstances;
        $this->sharedInstancesWithParams = &$diInstanceManager->sharedInstancesWithParams;
        $this->configurations = &$diInstanceManager->configurations;
        $this->typePreferences = &$diInstanceManager->typePreferences;
    }

    /**
     * Determine if we have a shared instance by class or alias
     *
     * @param $classOrAlias
     * @return bool
     */
    public function hasSharedInstance($classOrAlias)
    {
        return ($this->container->has($classOrAlias) || $this->diInstanceManager->hasSharedInstance($classOrAlias));
    }

    /**
     * Get shared instance
     *
     * @param $classOrAlias
     * @return mixed
     */
    public function getSharedInstance($classOrAlias)
    {
        if ($this->container->has($classOrAlias)) {
            return $this->container->get($classOrAlias);
        }

        return $this->diInstanceManager->getSharedInstance($classOrAlias);
    }
}
