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

use Doctrine\Common\Annotations\Reader;
use Ideea\FormExtraBundle\Annotation\FormField;
use Ideea\FormExtraBundle\Annotation\FormListener;
use Ideea\FormExtraBundle\Form\Metadata\FieldMetadata;
use Ideea\FormExtraBundle\Form\Metadata\FormMetadata;
use Ideea\FormExtraBundle\Form\Metadata\ListenerMetadata;

/**
 * Annotation metadata driver
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var array|\ReflectionClass[]
     */
    private static $reflections = array();

    /**
     * Construct
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritDoc}
     */
    public function isSupported($class)
    {
        if (isset(self::$reflections[$class])) {
            $reflection = self::$reflections[$class];
        } else {
            $reflection = new \ReflectionClass($class);
            self::$reflections[$class] = $reflection;
        }

        $classAnnotation = $this->reader->getClassAnnotation($reflection, 'Ideea\FormExtraBundle\Annotation\Form');

        if ($classAnnotation) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function read($class)
    {
        if (isset(self::$reflections[$class])) {
            $reflection = self::$reflections[$class];
        } else {
            $reflection = new \ReflectionClass($class);
            self::$reflections[$class] = $reflection;
        }

        $properties = $this->getAllProperties($reflection);

        /** @var \Ideea\FormExtraBundle\Annotation\Form $classAnnotation */
        $classAnnotation = $this->reader->getClassAnnotation($reflection, 'Ideea\FormExtraBundle\Annotation\Form');

        $fields = array();

        foreach ($properties as $property) {
            /** @var  $fieldAnnotation \Ideea\FormExtraBundle\Annotation\FormField */
            $fieldAnnotations = $this->reader->getPropertyAnnotations($property);

            foreach ($fieldAnnotations as $fieldAnnotation) {
                if ($fieldAnnotation instanceof FormField) {
                    $options = $fieldAnnotation->options;

                    $name = $fieldAnnotation->name;

                    if (!$name) {
                        $name = $property->getName();
                    }

                    if (null !== $fieldAnnotation->required) {
                        $options['required'] = $fieldAnnotation->required;
                    }

                    if (null !== $fieldAnnotation->label) {
                        $options['label'] = $fieldAnnotation->label;
                    }

                    $fieldMetadata = new FieldMetadata($fieldAnnotation->type, $name, $property->getName(), $options, $fieldAnnotation->choices, $fieldAnnotation->groups);
                    $fieldMetadata->position = $fieldAnnotation->position;
                    $fields[$name] = $fieldMetadata;
                }
            }
        }

        // Search root form listeners
        $methods = $this->getAllMethods($reflection, \ReflectionMethod::IS_PUBLIC);

        $listeners = array();
        foreach ($methods as $method) {
            /** @var \Ideea\FormExtraBundle\Annotation\FormListener $listenerAnnotation */
            $listenerAnnotations = $this->reader->getMethodAnnotations($method);

            foreach ($listenerAnnotations as $listenerAnnotation) {
                if ($listenerAnnotation instanceof FormListener) {
                    // Only static method supported
                    if (!$method->isStatic()) {
                        throw new \RuntimeException(sprintf(
                            'The method "%s" must be a static for use as event listener.',
                            $method->getName()
                        ));
                    }

                    $callback = array($class, $method->getName());
                    $listenerMetadata = new ListenerMetadata($callback, $listenerAnnotation->event, $listenerAnnotation->priority, $listenerAnnotation->groups);
                    $listeners[] = $listenerMetadata;
                }
            }
        }

        $formMetadata = new FormMetadata($classAnnotation->name, $fields, $classAnnotation->options, $listeners);

        return $formMetadata;
    }

    /**
     * Get all properties from reflection
     *
     * @param \ReflectionClass $class
     * @return array|\ReflectionProperty[]
     */
    private function getAllProperties(\ReflectionClass $class)
    {
        return $class->getProperties();
    }

    /**
     * Get all methods from reflection
     *
     * @param \ReflectionClass $class
     * @param int $mask
     * @return array|\ReflectionMethod[]
     */
    private function getAllMethods(\ReflectionClass $class, $mask = null)
    {
        return $class->getMethods($mask);
    }
}