<?php
/**
 * @see       https://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-servicemanager-di/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ServiceManager\Di;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiAbstractServiceFactory extends DiServiceFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $container, $name, $requestedName)
    {
        // Passing to createService in this instance to allow passing options
        // provided at invocation of the container.
        return $this->createService($container, $name, $requestedName);
    }

    /**
     * {@inheritDoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if ($this->instanceManager->hasSharedInstance($requestedName)
            || $this->instanceManager->hasAlias($requestedName)
            || $this->instanceManager->hasConfig($requestedName)
            || $this->instanceManager->hasTypePreferences($requestedName)
        ) {
            return true;
        }

        if (! $this->definitions->hasClass($requestedName) || interface_exists($requestedName)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $container, $name, $requestedName)
    {
        return $this->canCreate($container, $requestedName);
    }
}
