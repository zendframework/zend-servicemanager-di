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
use Zend\Di\Definition\DefinitionInterface;
use Zend\Di\Di;
use Zend\Di\InstanceManager;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiAbstractServiceFactoryTest extends TestCase
{
    /**
     * @var DiAbstractServiceFactory
     */
    protected $diAbstractServiceFactory;

    protected $fooInstance;
    protected $mockContainer;
    protected $mockDi;

    protected function setUp()
    {
        $instanceManager = new InstanceManager();
        $instanceManager->addSharedInstance($this->fooInstance = new stdClass(), 'foo');
        $this->mockDi = $this->getMockBuilder(Di::class)
            ->setConstructorArgs([null, $instanceManager])
            ->getMock();

        $this->mockContainer = $this->prophesize(ServiceLocatorInterface::class);
        $this->mockContainer->willImplement(ContainerInterface::class);

        $this->diAbstractServiceFactory = new DiAbstractServiceFactory($this->mockDi);
    }

    /**
     * @covers \Zend\ServiceManager\Di\DiAbstractServiceFactory::__construct
     */
    public function testConstructor()
    {
        $instance = new DiAbstractServiceFactory(
            $this->prophesize(Di::class)->reveal()
        );
        $this->assertInstanceOf(DiAbstractServiceFactory::class, $instance);
    }

    /**
     * @group 6021
     *
     * @covers \Zend\ServiceManager\Di\DiAbstractServiceFactory::createServiceWithName
     * @covers \Zend\ServiceManager\Di\DiAbstractServiceFactory::get
     */
    public function testCreateServiceWithNameAndWithoutRequestName()
    {
        $foo = $this->diAbstractServiceFactory->createServiceWithName(
            $this->mockContainer->reveal(),
            'foo',
            null
        );
        $this->assertEquals($this->fooInstance, $foo);
    }

    /**
     * @covers \Zend\ServiceManager\Di\DiAbstractServiceFactory::createServiceWithName
     * @covers \Zend\ServiceManager\Di\DiAbstractServiceFactory::get
     */
    public function testCreateServiceWithName()
    {
        $foo = $this->diAbstractServiceFactory->createServiceWithName(
            $this->mockContainer->reveal(),
            'foo',
            'foo'
        );
        $this->assertEquals($this->fooInstance, $foo);
    }

    /**
     * @covers \Zend\ServiceManager\Di\DiAbstractServiceFactory::canCreateServiceWithName
     */
    public function testCanCreateServiceWithName()
    {
        $instance = new DiAbstractServiceFactory(new Di());
        $im = $instance->instanceManager();

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        // will check shared instances
        $this->assertFalse($instance->canCreateServiceWithName(
            $container->reveal(),
            'a-shared-instance-alias',
            'a-shared-instance-alias'
        ));
        $im->addSharedInstance(new stdClass(), 'a-shared-instance-alias');
        $this->assertTrue($instance->canCreateServiceWithName(
            $container->reveal(),
            'a-shared-instance-alias',
            'a-shared-instance-alias'
        ));

        // will check aliases
        $this->assertFalse($instance->canCreateServiceWithName($container->reveal(), 'an-alias', 'an-alias'));
        $im->addAlias('an-alias', 'stdClass');
        $this->assertTrue($instance->canCreateServiceWithName($container->reveal(), 'an-alias', 'an-alias'));

        // will check instance configurations
        $this->assertFalse($instance->canCreateServiceWithName(
            $container->reveal(),
            __NAMESPACE__ . '\Non\Existing',
            __NAMESPACE__ . '\Non\Existing'
        ));
        $im->setConfig(__NAMESPACE__ . '\Non\Existing', ['parameters' => ['a' => 'b']]);
        $this->assertTrue($instance->canCreateServiceWithName(
            $container->reveal(),
            __NAMESPACE__ . '\Non\Existing',
            __NAMESPACE__ . '\Non\Existing'
        ));

        // will check preferences for abstract types
        $this->assertFalse($instance->canCreateServiceWithName(
            $container->reveal(),
            __NAMESPACE__ . '\AbstractClass',
            __NAMESPACE__ . '\AbstractClass'
        ));
        $im->setTypePreference(__NAMESPACE__ . '\AbstractClass', [__NAMESPACE__ . '\Non\Existing']);
        $this->assertTrue($instance->canCreateServiceWithName(
            $container->reveal(),
            __NAMESPACE__ . '\AbstractClass',
            __NAMESPACE__ . '\AbstractClass'
        ));

        // will check definitions
        $def = $instance->definitions();
        $this->assertFalse($instance->canCreateServiceWithName(
            $container->reveal(),
            __NAMESPACE__ . '\Other\Non\Existing',
            __NAMESPACE__ . '\Other\Non\Existing'
        ));

        $classDefinition = $this->prophesize(DefinitionInterface::class);
        $classDefinition->hasClass(__NAMESPACE__ . '\Other\Non\Existing')->willReturn(true);
        $classDefinition->getClasses()->willReturn([__NAMESPACE__ . '\Other\Non\Existing']);

        $def->addDefinition($classDefinition->reveal());
        $this->assertTrue($instance->canCreateServiceWithName(
            $container->reveal(),
            __NAMESPACE__ . '\Other\Non\Existing',
            __NAMESPACE__ . '\Other\Non\Existing'
        ));
    }
}
