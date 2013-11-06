<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity;

use IC\Bundle\Base\ComponentBundle\Entity\Entity as BaseEntity;

/**
 * Mock Entity
 *
 * @author Oleksii Strutsynksyi <oleksiis@nationalfibre.net>
 */
class Entity extends BaseEntity
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * Retrieve the id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
