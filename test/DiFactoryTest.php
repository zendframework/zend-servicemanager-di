<?php
/**
 * @see       https://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-servicemanager-di/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\ServiceManager\Di;

use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Zend\Di\Di;
use Zend\ServiceManager\Di\DiFactory;

class DiFactoryTest extends TestCase
{
    public function testWillInitializeDiAndDiAbstractFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn(['di' => ['']]);

        $factory = new DiFactory();
        $di = $factory($container->reveal(), Di::class);

        $this->assertInstanceOf(Di::class, $di);
    }
}
