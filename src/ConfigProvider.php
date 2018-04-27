<?php
/**
 * @see       https://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-servicemanager-di/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ServiceManager\Di;

use Zend\Di\LocatorInterface;

class ConfigProvider
{
    /**
     * Return configuration for applications.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Return dependency configuration.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return[
            'aliases' => [
                'Di'                    => 'DependencyInjector',
                LocatorInterface::class => 'DependencyInjector',
            ],
            'factories' => [
                'DependencyInjector'             => DiFactory::class,
                'DiAbstractServiceFactory'       => DiAbstractServiceFactoryFactory::class,
                'DiServiceInitializer'           => DiServiceInitializerFactory::class,
                'DiStrictAbstractServiceFactory' => DiStrictAbstractServiceFactoryFactory::class,
            ],
        ];
    }
}
