<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\Form\Extension;

use Ideea\FormExtraBundle\Form\EventSubscriber\AllowExtraFieldsSubscriber;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Allow extra fields extension
 */
class AllowExtraFieldsExtension extends AbstractTypeExtension
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => false
        ));

        $resolver->setAllowedTypes(array(
            'allow_extra_fields' => array('bool')
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (true === $options['allow_extra_fields']) {
            // Add subscriber for remove unused parameters
            $builder->addEventSubscriber(new AllowExtraFieldsSubscriber());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}