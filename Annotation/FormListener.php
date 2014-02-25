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
 * Indicate the form listener.
 *
 * @Annotation
 * @Target("METHOD")
 */
class FormListener
{
    /** @var string @Required */
    public $event;
    /** @var integer */
    public $priority = 0;
    /* @var array */
    public $groups = array();
}