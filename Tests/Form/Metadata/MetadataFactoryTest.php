<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\Tests\Form\Metadata;

use Ideea\FormExtraBundle\Form\Metadata\MetadataFactory;

/**
 * Metadata factory testing
 */
class MetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create driver
     *
     * @param string $classSupported
     * @param mixed $readValue
     * @return \Ideea\FormExtraBundle\Form\Metadata\Driver\DriverInterface
     */
    private function createDriverMock($classSupported, $readValue)
    {
        $mock = $this->getMockForAbstractClass('Ideea\FormExtraBundle\Form\Metadata\Driver\DriverInterface');
        $mock->expects($this->any())->method('isSupported')
            ->will($this->returnCallback(function ($class) use ($classSupported) {
                return $class == $classSupported;
            }));

        $mock->expects($this->any())->method('read')
            ->with($classSupported)->will($this->returnValue($readValue));

        return $mock;
    }

    /**
     * Test factory with drivers not found
     *
     * @expectedException \Ideea\FormExtraBundle\Exception\DriverNotFoundException
     */
    public function testWithDriverNotFound()
    {
        $factory = new MetadataFactory();
        $factory->getFormMetadata(__NAMESPACE__ . '\TestedModel');
    }

    /**
     * Test with many drivers
     */
    public function testWithManyDrivers()
    {
        $driver1 = $this->createDriverMock(__NAMESPACE__ . '\TestedModel1', 'driver1');
        $driver2 = $this->createDriverMock(__NAMESPACE__ . '\TestedModel2', 'driver2');

        $factory = new MetadataFactory();
        $factory->addDriver($driver1);
        $factory->addDriver($driver2);

        $readValue = $factory->getFormMetadata(__NAMESPACE__ . '\TestedModel2');
        $this->assertEquals('driver2', $readValue);

        $readValue = $factory->getFormMetadata(__NAMESPACE__ . '\TestedModel1');
        $this->assertEquals('driver1', $readValue);
    }
}

/**
 * Tested models
 */
class TestedModel1
{
}

/**
 * Tested models
 */
class TestedModel2
{
}