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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Control allow extra fields
 */
class AllowExtraFieldsSubscriber implements EventSubscriberInterface
{
    /**
     * Diff submitted data. Remove unused parameters
     *
     * @param FormEvent $event
     * @throws \RuntimeException
     */
    public function diffSubmittedData(FormEvent $event)
    {
        $submittedData = $event->getData();
        $form = $event->getForm();

        if (!$form->isRoot()) {
            throw new \RuntimeException('Can\'t remove unused parameters in non-root form');
        }

        if (is_object($submittedData)) {
            // Clone submitted data for disable override variables
            $submittedData = clone ($submittedData);
        }

        foreach ($submittedData as $key => $value) {
            if (!$form->has($key)) {
                unset ($submittedData[$key]);
            }
        }

        $event->setData($submittedData);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => array('diffSubmittedData')
        );
    }
}