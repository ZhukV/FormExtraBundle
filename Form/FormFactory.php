<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\Form;

use Ideea\FormExtraBundle\Form\Metadata\MetadataFactory;
use Symfony\Component\Form\FormFactory as BaseFormFactory;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeFactoryInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * Override base form factory for generated form from objects
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class FormFactory extends BaseFormFactory
{
    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * Construct
     *
     * @param FormRegistryInterface $registry
     * @param ResolvedFormTypeFactoryInterface $resolvedTypeFactory
     * @param MetadataFactory $metadataFactory
     */
    public function __construct(FormRegistryInterface $registry, ResolvedFormTypeFactoryInterface $resolvedTypeFactory, MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;

        parent::__construct($registry, $resolvedTypeFactory);
    }

    /**
     * {@inheritDoc}
     */
    public function createBuilder($type = 'form', $data = null, array $options = array())
    {
        $originalType = $type;

        if ($this->checkFormType($type, $data, $options)) {
            $class = is_object($originalType) ? get_class($originalType) : $originalType;
            $formMetadata = $this->metadataFactory->getFormMetadata($class);

            if ($formMetadata->getName()) {
                return parent::createNamedBuilder($formMetadata->getName(), $type, $data, $options);
            }
        }

        return parent::createBuilder($type, $data, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function createNamedBuilder($name, $type = 'form', $data = null, array $options = array())
    {
        $this->checkFormType($type, $data, $options);

        return parent::createNamedBuilder($name, $type, $data, $options);
    }

    /**
     * Check form type
     *
     * @param mixed $type
     * @param mixed $data
     * @param array $options
     * @return bool     Indicate of generated form
     */
    private function checkFormType(&$type, &$data, array &$options)
    {
        if (is_object($type)) {
            if (!$type instanceof FormTypeInterface && !$type instanceof ResolvedFormTypeInterface) {
                // Not base form. Try load metadata info
                if ($this->metadataFactory->isSupported(get_class($type))) {
                    $options['data_class'] = get_class($type);
                    $data = $type;
                    $type = 'model';

                    return true;
                }
            }

            return false;
        }

        if (class_exists($type)) {
            if ($this->metadataFactory->isSupported($type)) {
                $options['data_class'] = $type;
                $type = 'model';

                return true;
            }
        }

        return false;
    }
}