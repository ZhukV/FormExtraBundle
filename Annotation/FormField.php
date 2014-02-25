<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\Annotation;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * Form field annotation
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class FormField
{
    /** @var string @Required */
    public $type = 'text';
    /** @var string */
    public $name;
    /** @var string */
    public $label;
    /** @var bool */
    public $required;
    /** @var integer */
    public $position = 0;
    /** @var array<mixed> */
    public $options = array();
    /** @var array */
    public $choices = null; // Only choice type used
    /** @var array */
    public $groups = array('Default');

    /**
     * Construct
     *
     * @param array $values
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->type = $values['value'];
            unset ($values['value']);
        }

        $objectProperties = array_keys(get_object_vars($this));

        foreach ($values as $key => $value) {
            if (!in_array($key, $objectProperties)) {
                throw new AnnotationException(sprintf(
                    'Not found field "%s" in annotation "%s".',
                    $key, __CLASS__
                ));
            }

            $this->{$key} = $value;
        }

        if (null !== $this->choices && $this->type != 'choice') {
            throw new AnnotationException('Can\'t use "choices" parameter. This parameter can be use only "choice" type.');
        }
    }
}