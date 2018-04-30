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
use Zend\Di\Di;
use Zend\Di\InstanceManager;
use Zend\ServiceManager\Di\DiInstanceManagerProxy;
use Zend\ServiceManager\Di\DiServiceInitializer;

class DiServiceInitializerTest extends TestCase
{
    /**
     * @var DiServiceInitializer
     */
    protected $diServiceInitializer;

    protected $mockContainer;
    protected $mockDi;
    protected $mockDiInstanceManagerProxy;
    protected $mockDiInstanceManager;

    protected function setUp()
    {
        $this->mockDi = $this->prophesize(Di::class);

        $this->mockContainer = $this->prophesize(ServiceLocatorInterface::class);
        $this->mockContainer->willImplement(ContainerInterface::class);

        $this->mockDiInstanceManager = $this->prophesize(InstanceManager::class);

        $this->mockDiInstanceManagerProxy = new DiInstanceManagerProxy(
            $this->mockDiInstanceManager->reveal(),
            $this->mockContainer->reveal()
        );

        $this->diServiceInitializer = new DiServiceInitializer(
            $this->mockDi->reveal(),
            $this->mockContainer->reveal(),
            $this->mockDiInstanceManagerProxy
        );
    }

    /**
     * @covers \Zend\ServiceManager\Di\DiServiceInitializer::__invoke
     */
    public function testInitializeUsingV2Api()
    {
        $instance = new stdClass();

        // test di is called with proper instance
        $this->mockDi->injectDependencies($instance)->shouldBeCalled();

        $this->diServiceInitializer->__invoke($instance, $this->mockContainer->reveal());
    }

    /**
     * @covers \Zend\ServiceManager\Di\DiServiceInitializer::__invoke
     */
    public function testInitializeUsingV3Api()
    {
        $instance = new stdClass();

        // test di is called with proper instance
        $this->mockDi->injectDependencies($instance)->shouldBeCalled();

        $this->diServiceInitializer->__invoke($this->mockContainer->reveal(), $instance);
    }

    /**
     * @covers \Zend\ServiceManager\Di\DiServiceInitializer::__invoke
     * @todo this needs to be moved into its own class
     */
    public function testProxyInstanceManagersStayInSync()
    {
        $instanceManager = new InstanceManager();
        $proxy = new DiInstanceManagerProxy($instanceManager, $this->mockContainer->reveal());
        $instanceManager->addAlias('foo', 'bar');

        $this->assertEquals($instanceManager->getAliases(), $proxy->getAliases());
    }
}
