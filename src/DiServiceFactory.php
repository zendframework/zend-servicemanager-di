<?php
/**
 * @link      http://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager\Di;

use Interop\Container\ContainerInterface;
use Zend\Di\Di;
use Zend\Di\Exception\ClassNotFoundException as DiClassNotFoundException;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for pulling a service from a DI container.
 *
 * This factory can be mapped to arbitrary class names, and used to pull them
 * from the composed Di instance, using the following behaviors:
 *
 * - If USE_SL_BEFORE_DI is passed as the second argument to the constructor,
 *   the factory will attempt to fetch the service from the passed container
 *   first, and fall back to the composed DI container only on failure.
 * - If USE_SL_AFTER_DI is passed as the second argument to the constructor,
 *   the factory will attempt to fetch the service from the composed DI
 *   container first, and fall back to the passed container only on failure.
 * - If USE_SL_NONE is passed as the second argument to the constructor (or no
 *   argument is passed), then the factory will only fetch from the composed
 *   DI container.
 *
 * The DiAbstractServiceFactory extends this class in order to
 * return and/or configure instances.
 */
class DiServiceFactory extends Di implements FactoryInterface
{
    /**@#+
     * constants
     */
    const USE_SL_BEFORE_DI = 'before';
    const USE_SL_AFTER_DI  = 'after';
    const USE_SL_NONE      = 'none';
    /**@#-*/

    /**
     * @var ContainerInterface
     */
    protected $container = null;

    /**
     * zend-servicemanager v2 support for factory creation options.
     *
     * @var array
     */
    protected $creationOptions = [];

    /**
     * @var Di
     */
    protected $di = null;

    /**
     * @var string
     */
    protected $useContainer = self::USE_SL_AFTER_DI;

    /**
     * @param Di $di
     * @param string $useContainer
     */
    public function __construct(Di $di, $useContainer = self::USE_SL_NONE)
    {
        $this->di = $di;

        if (in_array($useContainer, [self::USE_SL_BEFORE_DI, self::USE_SL_AFTER_DI, self::USE_SL_NONE])) {
            $this->useContainer = $useContainer;
        }

        // since we are using this in a proxy-fashion, localize state
        $this->definitions = $this->di->definitions;
        $this->instanceManager = $this->di->instanceManager;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $this->container = $container;
        return $this->get($name, $options);
    }

    /**
     * zend-servicemanager v2 compatibility.
     *
     * @param ServiceLocatorInterface $container
     * @param null|string $name
     * @param null|string $requestedName
     * @return object
     */
    public function createService(ServiceLocatorInterface $container, $name = null, $requestedName = null)
    {
        return $this($container, $requestedName ?: $name, $this->creationOptions);
    }

    /**
     * zend-servicemanager v2 support for options passed to factory.
     *
     * @param array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }

    /**
     * Override, as we want it to use the functionality defined in the proxy.
     *
     * @param string $name
     * @param array $params
     * @return object
     * @throws Exception\ServiceNotFoundException
     */
    public function get($name, array $params = [])
    {
        // Allow this di service to get dependencies from the service locator BEFORE trying DI.
        if ($this->useContainer == self::USE_SL_BEFORE_DI && $this->container->has($name)) {
            return $this->container->get($name);
        }

        try {
            return parent::get($name, $params);
        } catch (DiClassNotFoundException $e) {
            // allow this di service to get dependencies from the service locator AFTER trying di
            if ($this->useContainer !== self::USE_SL_AFTER_DI || $this->container->has($name)) {
                throw new Exception\ServiceNotFoundException(
                    sprintf('Service %s was not found in this DI instance', $name),
                    null,
                    $e
                );
            }
        }

        return $this->container->get($name);
    }
}
