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
 * Field metadata for generate field in form
 */
class FieldMetadata extends GroupingMetadata
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $choices;

    /**
     * @var integer
     */
    public $position = 0;

    /**
     * Construct
     *
     * @param string $type
     * @param string $name
     * @param string $propertyName
     * @param array $options
     * @param array $choices
     * @param array $groups
     */
    public function __construct($type, $name, $propertyName, array $options, $choices, array $groups)
    {
        $this->type = $type;
        $this->name = $name;
        $this->propertyName = $propertyName;
        $this->options = $options;
        $this->choices = $choices;
        $this->groups = $groups;
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
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get propertyName
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
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
     * Get choices
     *
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }
}