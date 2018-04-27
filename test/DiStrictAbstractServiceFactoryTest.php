<?php
/**
 * @see       https://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-servicemanager-di/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\ServiceManager\Di;

use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\Di\Config;
use Zend\Di\Di;
use Zend\ServiceManager\Di\DiStrictAbstractServiceFactory;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiStrictAbstractServiceFactoryTest extends TestCase
{
    public function testSetGetAllowedServiceNames()
    {
        $instance = new DiStrictAbstractServiceFactory($this->prophesize(Di::class)->reveal());
        $instance->setAllowedServiceNames(['first-service', 'second-service']);
        $allowedServices = $instance->getAllowedServiceNames();
        $this->assertCount(2, $allowedServices);
        $this->assertContains('first-service', $allowedServices);
        $this->assertContains('second-service', $allowedServices);
    }

    public function testWillOnlyCreateServiceInWhitelist()
    {
        $instance = new DiStrictAbstractServiceFactory(new Di());
        $instance->setAllowedServiceNames(['a-whitelisted-service-name']);
        $im = $instance->instanceManager();
        $im->addSharedInstance(new stdClass(), 'a-whitelisted-service-name');

        $locator = $this->prophesize(ServiceLocatorInterface::class);
        $locator->willImplement(ContainerInterface::class);

        $this->assertTrue($instance->canCreateServiceWithName(
            $locator->reveal(),
            'a-whitelisted-service-name',
            'a-whitelisted-service-name'
        ));
        $this->assertInstanceOf(
            'stdClass',
            $instance->createServiceWithName(
                $locator->reveal(),
                'a-whitelisted-service-name',
                'a-whitelisted-service-name'
            )
        );

        $this->assertFalse($instance->canCreateServiceWithName(
            $locator->reveal(),
            'not-whitelisted',
            'not-whitelisted'
        ));

        $this->expectException(InvalidServiceException::class);
        $instance->createServiceWithName($locator->reveal(), 'not-whitelisted', 'not-whitelisted');
    }

    public function testWillFetchDependenciesFromServiceManagerBeforeDi()
    {
        $controllerName = TestAsset\ControllerWithDependencies::class;
        $config = new Config([
            'instance' => [
                $controllerName => ['parameters' => ['injected' => 'stdClass']],
            ],
        ]);

        $testService = new stdClass();

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);
        $container->has(stdClass::class)->willReturn(true);
        $container->get(stdClass::class)->willReturn($testService);

        $di = new Di();
        $config->configure($di);
        $instance = new DiStrictAbstractServiceFactory($di, DiStrictAbstractServiceFactory::USE_SL_BEFORE_DI);
        $instance->setAllowedServiceNames([$controllerName]);
        $service = $instance->createServiceWithName(
            $container->reveal(),
            $controllerName,
            $controllerName
        );
        $this->assertSame($testService, $service->injectedValue);
    }
}
