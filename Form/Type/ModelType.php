<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\Form\Type;

use Ideea\FormExtraBundle\Form\EventSubscriber\FormGeneratorSubscriber;
use Ideea\FormExtraBundle\Form\Metadata\MetadataFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Auto generated model type
 */
class ModelType extends AbstractType
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
     * Construct
     *
     * @param MetadataFactory $metadataFactory
     * @param ContainerInterface $container
     */
    public function __construct(MetadataFactory $metadataFactory, ContainerInterface $container)
    {
        $this->metadataFactory = $metadataFactory;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'generator_groups' => 'Default'
        ));

        $resolver->setAllowedTypes(array(
            'data_class' => array('string'), // The parameter "data_class" is required,
            'generator_groups' => array('string', 'array')
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Check metadata exists
        $dataClass = $options['data_class'];

        if (!$this->metadataFactory->isSupported($dataClass)) {
            throw new \RuntimeException(sprintf(
                'Could not build model. The class "%s" not supported.',
                $dataClass
            ));
        }

        $formMetadata = $this->metadataFactory->getFormMetadata($dataClass);

        $generatorGroups = $options['generator_groups'];

        // Add root listeners
        foreach ($formMetadata->getListeners() as $rootListener) {
            if ($rootListener->checkExistsGroups($generatorGroups)) {
                $builder->addEventListener($rootListener->getEvent(), $rootListener->getCallback(), $rootListener->getPriority());
            }
        }

        // Create a generator subscriber and set to form builder
        $generatorSubscriber = new FormGeneratorSubscriber($this->metadataFactory, $this->container, (array) $options['generator_groups']);
        $builder->addEventSubscriber($generatorSubscriber);
    }

    /**
     * {@inheritDpc}
     */
    public function getName()
    {
        return 'model';
    }
}