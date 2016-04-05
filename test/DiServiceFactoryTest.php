<?php
/**
 * @link      http://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager\Di;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
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
    protected $diServiceFactory = null;

    protected $mockContainer = null;
    protected $mockDi = null;
    protected $fooInstance = null;

    public function setup()
    {
        $instanceManager = new InstanceManager();
        $instanceManager->addSharedInstanceWithParameters(
            $this->fooInstance = new stdClass(),
            'foo',
            ['bar' => 'baz']
        );
        $this->mockDi = $this->getMock(Di::class, [], [null, $instanceManager]);

        $this->mockContainer = $this->prophesize(ServiceLocatorInterface::class);
        $this->mockContainer->willImplement(ContainerInterface::class);

        $this->diServiceFactory = new DiServiceFactory(
            $this->mockDi,
            ['bar' => 'baz']
        );
    }

    /**
     * @covers Zend\ServiceManager\Di\DiServiceFactory::__construct
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
     * @covers Zend\ServiceManager\Di\DiServiceFactory::createService
     * @covers Zend\ServiceManager\Di\DiServiceFactory::get
     */
    public function testCreateService()
    {
        // check if v2 vs v3
        $foo = $this->diServiceFactory->__invoke($this->mockContainer->reveal(), 'foo', ['bar' => 'baz']);
        $this->assertEquals($this->fooInstance, $foo);
    }
}
