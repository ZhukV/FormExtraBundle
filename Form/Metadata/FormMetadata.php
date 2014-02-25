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

/**
 * Self form metadata
 */
class FormMetadata
{
    /**
     * @var array|FieldMetadata[]
     */
    private $fields = array();

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array|ListenerMetadata[]
     */
    private $listeners;

    /**
     * Construct
     *
     * @param string $name
     * @param array|FieldMetadata[] $fields
     * @param array $options
     * @param array|ListenerMetadata[] $listeners
     */
    public function __construct($name, array $fields, array $options, array $listeners)
    {
        $this->name = $name;
        $this->fields = $fields;
        $this->options = $options;
        $this->listeners = $listeners;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get fields
     *
     * @return array|FieldMetadata[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get listeners
     *
     * @return array|ListenerMetadata[]
     */
    public function getListeners()
    {
        return $this->listeners;
    }
}