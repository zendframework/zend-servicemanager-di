<?php
/**
 * @link      http://github.com/zendframework/zend-servicemanager-di for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
