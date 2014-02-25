<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\Form\Metadata;

use Ideea\FormExtraBundle\Exception\DriverNotFoundException;
use Ideea\FormExtraBundle\Form\Metadata\Driver\DriverInterface;

/**
 * Factory form generate metadata form info
 */
class MetadataFactory
{
    /**
     * @var array|DriverInterface[]
     */
    private $drivers = array();

    /**
     * Add driver
     *
     * @param DriverInterface $driver
     * @return MetadataFactory
     */
    public function addDriver(DriverInterface $driver)
    {
        $this->drivers[] = $driver;

        return $this;
    }

    /**
     * Get driver for class
     *
     * @param string $class
     * @throws \Ideea\FormExtraBundle\Exception\DriverNotFoundException
     * @return DriverInterface
     */
    protected function getDriverForClass($class)
    {
        foreach ($this->drivers as $driver) {
            if ($driver->isSupported($class)) {
                return $driver;
            }
        }

        throw new DriverNotFoundException(sprintf(
            'Not found driver for class "%s".',
            $class
        ));
    }

    /**
     * Is class supported
     *
     * @param string $class
     * @return bool
     */
    public function isSupported($class)
    {
        try {
            $this->getDriverForClass($class);
            return true;
        } catch (DriverNotFoundException $e) {
            return false;
        }
    }

    /**
     * Get form metadata
     *
     * @param string $class
     * @return FormMetadata
     */
    public function getFormMetadata($class)
    {
        $driver = $this->getDriverForClass($class);
        return $driver->read($class);
    }
}