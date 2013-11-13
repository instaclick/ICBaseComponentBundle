<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity;

use IC\Bundle\Base\ComponentBundle\Entity\Entity as BaseEntity;

/**
 * Mock Entity
 *
 * @author Dhaval Patel <dhavalp@nationalfibre.net>
 */
class User extends BaseEntity
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * Retrieve the screenname.
     *
     * @return string
     */
    public function getScreenName()
    {
        return 'mock screenName';
    }
}
