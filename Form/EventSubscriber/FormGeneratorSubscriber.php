<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\Form\EventSubscriber;

use Ideea\FormExtraBundle\Form\Metadata\FieldMetadata;
use Ideea\FormExtraBundle\Form\Metadata\MetadataFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Subscriber for generate form from metadata information
 */
class FormGeneratorSubscriber implements EventSubscriberInterface
{
    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $generatorGroups;

    /**
     * Construct
     *
     * @param MetadataFactory $metadataFactory
     * @param ContainerInterface $container
     * @param array $generatorGroups
     */
    public function __construct(MetadataFactory $metadataFactory, ContainerInterface $container, array $generatorGroups)
    {
        $this->metadataFactory = $metadataFactory;
        $this->container = $container;
        $this->generatorGroups = $generatorGroups;
    }

    /**
     * Build form from metadata info
     *
     * @param FormEvent $event
     * @throws \RuntimeException
     */
    public function buildForm(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $dataClass = $form->getConfig()->getDataClass();

        $formMetadata = $this->metadataFactory->getFormMetadata($dataClass);

        $fields = $formMetadata->getFields();
        uasort($fields, function (FieldMetadata $a, FieldMetadata $b) {
            if ($a->position == $b->position) {
                return 0;
            }

            return $a->position > $b->position ? 1 : -1;
        });

        $formFactory = $form->getConfig()->getFormFactory();

        foreach ($fields as $fieldMetadata) {
            // Check generator group
            if (!$fieldMetadata->checkExistsGroups($this->generatorGroups)) {
                continue;
            }

            $options = $fieldMetadata->getOptions();
            $name = $fieldMetadata->getName();
            $type = $fieldMetadata->getType();

            $propertyPath = $fieldMetadata->getPropertyName();
            if (!empty($options['property_path'])) {
                $propertyPath = $options['property_path'];
            } else {
                $options['property_path'] = $propertyPath;
            }

            $options['auto_initialize'] = false;

            if ('choice' === $type) {
                if ($choices = $fieldMetadata->getChoices()) {
                    $options['choices'] = $this->getChoices($choices, $dataClass);
                }
            } else if ('collection' === $type) {
                if (!empty($options['type'])) {
                    $childType = $options['type'];

                    if (class_exists($childType)) {
                        if ($this->metadataFactory->isSupported($childType)) {
                            $options['type'] = 'model';
                            $options['options']['data_class'] = $childType;
                        }
                    }
                }
            }

            // Get data for child field
            $childData = $this->getValue($propertyPath, $data);

            $child = $formFactory->createNamed($name, $type, $childData, $options);
            $form->add($child);
        }
    }

    /**
     * Get value from data
     *
     * @param string $propertyPath
     * @param object $model
     * @return mixed
     */
    private function getValue($propertyPath, $model)
    {
        static $accessor;

        if (!$model) {
            return null;
        }

        if (!$accessor) {
            $accessor = new PropertyAccessor();
        }

        return $accessor->getValue($model, $propertyPath);
    }

    /**
     * Get choices option for "choice" field type
     *
     * @param array|string $choices
     * @param string $dataClass
     * @throws \RuntimeException    If callback is not callable
     * @return array|null
     */
    private function getChoices($choices, $dataClass)
    {
        if (is_array($choices)) {
            if (is_callable($choices)) {
                $choices = call_user_func($choices);
            }
        } else {
            // Try parse from format: "Acme\Demo\CustomClass:methodName" or "custom.service:methodName"
            $parts = explode(':', $choices);
            if (count($parts) != 2) {
                throw new \RuntimeException(sprintf(
                    'Could not parse callback. Must be format "Acme\CustomClass:methodName" or "custom.service:methodName", "%s" given.',
                    $choices
                ));
            }

            if ($parts[0] == 'static') {
                $parts[0] = $dataClass;
            }

            if (class_exists($parts[0])) {
                $callback = $parts;
            } else if ($this->container->has($parts[0])) {
                $callback = array($this->container->get($parts[0]), $parts[1]);
            } else {
                throw new \RuntimeException(sprintf(
                    'The callback "%s: is not callable.',
                    $choices
                ));
            }

            $choices = call_user_func($callback);
        }

        return $choices;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => array('buildForm', 512)
        );
    }
}