<?php
/**
 * @see       https://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-servicemanager-di/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\ServiceManager\Di;

use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\Di\Di;
use Zend\Di\InstanceManager;
use Zend\ServiceManager\Di\DiServiceFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiServiceFactoryTest extends TestCase
{
    /**
     * @covers \Zend\ServiceManager\Di\DiServiceFactory::__construct
     */
    public function testConstructor()
    {
        $instance = new DiServiceFactory(
            $this->prophesize(Di::class)->reveal()
        );
        $this->assertInstanceOf(DiServiceFactory::class, $instance);
    }

    /**
     * @covers \Zend\ServiceManager\Di\DiServiceFactory::createService
     * @covers \Zend\ServiceManager\Di\DiServiceFactory::get
     */
    public function testCreateService()
    {
        $fooInstance = new stdClass();

        $instanceManager = new InstanceManager();
        $instanceManager->addSharedInstanceWithParameters(
            $fooInstance,
            'foo',
            ['bar' => 'baz']
        );

        $container = $this->prophesize(ContainerInterface::class);

        $di = new Di(null, $instanceManager);
        $diServiceFactory = new DiServiceFactory($di);

        $foo = $diServiceFactory->__invoke($container->reveal(), 'foo', ['bar' => 'baz']);
        $this->assertSame($fooInstance, $foo);
    }

    public function testCreateServiceWithNullOptions()
    {
        $fooInstance = new stdClass();

        $instanceManager = new InstanceManager();
        $instanceManager->addSharedInstance($fooInstance, 'foo');

        $di = new Di(null, $instanceManager);
        $diServiceFactory = new DiServiceFactory($di);

        $container = $this->prophesize(ContainerInterface::class);

        $foo = $diServiceFactory->__invoke($container->reveal(), 'foo');
        $this->assertSame($fooInstance, $foo);
    }

    public function testCreateServiceV2V3()
    {
        $fooInstance = new stdClass();

        $instanceManager = new InstanceManager();
        $instanceManager->addSharedInstanceWithParameters(
            $fooInstance,
            'foo',
            ['bar' => 'baz']
        );

        $container = $this->prophesize(ServiceLocatorInterface::class)
            ->willImplement(ContainerInterface::class);

        $di = new Di(null, $instanceManager);
        $diServiceFactoryV3 = new DiServiceFactory($di);
        $diServiceFactoryV2 = new DiServiceFactory($di);
        $diServiceFactoryV2->setCreationOptions(['bar' => 'baz']);

        $fooV3 = $diServiceFactoryV3->__invoke($container->reveal(), 'foo', ['bar' => 'baz']);
        $this->assertSame($fooInstance, $fooV3);

        $fooV2 = $diServiceFactoryV2->createService($container->reveal(), 'foo');
        $this->assertSame($fooInstance, $fooV2);

        $this->assertSame($fooV3, $fooV2);
    }
}
