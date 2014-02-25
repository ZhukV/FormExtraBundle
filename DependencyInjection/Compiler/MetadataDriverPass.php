<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compile metadata drivers
 */
class MetadataDriverPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $metadataFactory = $container->getDefinition('ideea.form_extra.metadata_factory');

        foreach ($container->findTaggedServiceIds('form_extra.metadata.driver') as $id => $attributes) {
            $driver = $container->getDefinition($id);

            try {
                $class = $driver->getClass();
                $class = $container->getParameterBag()->resolveValue($class);

                $reflection = new \ReflectionClass($class);
                $requiredInterface = 'Ideea\FormExtraBundle\Form\Metadata\Driver\DriverInterface';

                if (!$reflection->implementsInterface($requiredInterface)) {
                    throw new \RuntimeException(sprintf(
                        'The driver with service id "%s" must be implement of "%s" interface.',
                        $id,
                        $requiredInterface
                    ));
                }
            } catch (\Exception $e) {
                throw new \RuntimeException(sprintf(
                    'Could not compile driver with service id "%s".',
                    $id
                ), 0, $e);
            }

            $metadataFactory->addMethodCall('addDriver', array(new Reference($id)));
        }
    }
}