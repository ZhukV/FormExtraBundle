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

/**
 * @Annotation
 * @Target("CLASS")
 */
class Form
{
    /** @var string @Required */
    public $name;
    /** @var array */
    public $options = array();
}