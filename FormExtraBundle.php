<?php

/**
* This file is part of the FormExtraBundle package
*
* (c) Vitaliy Zhuk <zhuk2205@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code
*/

namespace Ideea\FormExtraBundle;

use Ideea\FormExtraBundle\DependencyInjection\Compiler\MetadataDriverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FormExtraBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MetadataDriverPass());
    }
}