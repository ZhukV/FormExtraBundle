<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\Form\Metadata\Driver;

/**
 * Control driver load metadata
 */
interface DriverInterface
{
    /**
     * Is class supported
     *
     * @param string $class
     * @return bool
     */
    public function isSupported($class);

    /**
     * Read metadata
     *
     * @param string $class
     * @return \Ideea\FormExtraBundle\Form\Metadata\FormMetadata
     */
    public function read($class);
}