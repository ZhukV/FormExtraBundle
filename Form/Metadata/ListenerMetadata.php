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
 * Control listeners in forms
 */
class ListenerMetadata extends GroupingMetadata
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @var string
     */
    private $event;

    /**
     * @var integer
     */
    private $priority;

    /**
     * Construct
     *
     * @param callable $callback
     * @param string $event
     * @param int $priority
     * @param array $groups
     * @throws \InvalidArgumentException
     */
    public function __construct($callback, $event, $priority = 0, array $groups)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('The callback must be a callable.');
        }

        if (!self::isEventExists($event)) {
            throw new \InvalidArgumentException(sprintf(
                'The event "%s" not available. Please see Symfony\Component\Form\FormEvents for view all available events.',
                $event
            ));
        }

        $this->callback = $callback;
        $this->event = $event;
        $this->priority = $priority;
        $this->groups = $groups;
    }

    /**
     * Check event exists in FormEvents
     *
     * @param string $event
     * @return bool
     */
    protected static function isEventExists($event)
    {
        static $events;

        if (!$events) {
            $ref = new \ReflectionClass('Symfony\Component\Form\FormEvents');
            $events = $ref->getConstants();
        }

        return in_array($event, $events);
    }

    /**
     * Get callback
     *
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}