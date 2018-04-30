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

class DiServiceInitializerFactory implements FactoryInterface
{
    /**
     * Class responsible for instantiating a DiServiceInitializer
     *
     * @param ContainerInterface $container
     * @param string $name
     * @param null|array $options
     * @return DiServiceInitializer
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        return new DiServiceInitializer($container->get('Di'), $container);
    }

    /**
     * Create and return DiServiceInitializer instance
     *
     * For use with zend-servicemanager v2; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return DiServiceInitializer
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, DiServiceInitializer::class);
    }
}
