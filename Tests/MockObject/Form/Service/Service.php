<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\MockObject\Form\Service;

/**
 * Dummy form service
 *
 * @author Anthon Pang <anthonp@nationalfibre.net>
 */
class Service
{
    /**
     * Service method
     *
     * @param mixed $model
     *
     * @return boolean
     */
    public function execute($model)
    {
        return true;
    }
}
