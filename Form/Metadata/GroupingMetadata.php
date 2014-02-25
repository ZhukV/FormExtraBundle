<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\Form\Metadata;

/**
 * Control grouping metadata
 */
abstract class GroupingMetadata
{
    /**
     * @var array
     */
    protected $groups = array();

    /**
     * Get generator groups
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Check exists group
     *
     * @param string|array $groups
     * @return bool
     */
    public function checkExistsGroups($groups)
    {
        if (!is_array($groups)) {
            $groups = array($groups);
        }

        foreach ($groups as $group) {
            if (in_array($group, $this->groups)) {
                return true;
            }
        }

        return false;
    }
}