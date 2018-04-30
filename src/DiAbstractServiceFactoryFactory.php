<?php
/**
 * @see       https://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-servicemanager-di/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ServiceManager\Di;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class DiAbstractServiceFactoryFactory implements FactoryInterface
{
    /**
     * Class responsible for instantiating a DiAbstractServiceFactory
     *
     * @param ContainerInterface $container
     * @param string $name
     * @param null|array $options
     * @return DiAbstractServiceFactory
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $factory = new DiAbstractServiceFactory($container->get('Di'), DiAbstractServiceFactory::USE_SL_BEFORE_DI);

        if ($container instanceof ServiceManager) {
            $container->addAbstractFactory($factory, false);
        }

        return $factory;
    }

    /**
     * Create and return DiAbstractServiceFactory instance
     *
     * For use with zend-servicemanager v2; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return DiAbstractServiceFactory
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, DiAbstractServiceFactory::class);
    }
}
