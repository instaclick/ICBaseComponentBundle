<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\EventListener;

/**
 * Entity Listener interface
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 */
interface EntityListenerInterface
{
    /**
     * Check if a given Entity is listening to event
     *
     * @param object $entity
     *
     * @return boolean
     */
    public function isListenedTo($entity);
}
