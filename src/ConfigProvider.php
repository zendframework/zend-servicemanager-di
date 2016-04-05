<?php
/**
 * @link      http://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
