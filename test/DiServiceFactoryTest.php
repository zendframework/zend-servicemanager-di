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
use Zend\ServiceManager\Di\DiServiceFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiServiceFactoryTest extends TestCase
{
    /**
     * @var DiServiceFactory
     */
    protected $diServiceFactory;

    protected $mockContainer;
    protected $mockDi;
    protected $fooInstance;

    protected function setUp()
    {
        $instanceManager = new InstanceManager();
        $instanceManager->addSharedInstanceWithParameters(
            $this->fooInstance = new stdClass(),
            'foo',
            ['bar' => 'baz']
        );
        $this->mockDi = $this->getMockBuilder(Di::class)
            ->setConstructorArgs([null, $instanceManager])
            ->getMock();

        $this->mockContainer = $this->prophesize(ServiceLocatorInterface::class);
        $this->mockContainer->willImplement(ContainerInterface::class);

        $this->diServiceFactory = new DiServiceFactory(
            $this->mockDi,
            ['bar' => 'baz']
        );
    }

    /**
     * @covers \Zend\ServiceManager\Di\DiServiceFactory::__construct
     */
    public function testConstructor()
    {
        $instance = new DiServiceFactory(
            $this->prophesize(Di::class)->reveal(),
            ['foo' => 'bar']
        );
        $this->assertInstanceOf(DiServiceFactory::class, $instance);
    }

    /**
     * @covers \Zend\ServiceManager\Di\DiServiceFactory::createService
     * @covers \Zend\ServiceManager\Di\DiServiceFactory::get
     */
    public function testCreateService()
    {
        // check if v2 vs v3
        $foo = $this->diServiceFactory->__invoke($this->mockContainer->reveal(), 'foo', ['bar' => 'baz']);
        $this->assertEquals($this->fooInstance, $foo);
    }
}
