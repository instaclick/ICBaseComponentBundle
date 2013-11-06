<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\MockObject\Form\Model;

use DMS\Filter\Rules as Filter;
use IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity;
use IC\Bundle\Base\ComponentBundle\Validator\Constraints as BaseAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mock Form Model
 *
 * @author Oleksii Strutsynskyi <oleksiis@nationalfibre.net>
 *
 * @BaseAssert\EntityNotIdentical(sourceEntity="source", targetEntity="target")
 */
class EntityNotIdenticalFormModel
{
    /**
     * @Assert\Valid()
     *
     * @var \IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity
     */
    private $source;

    /**
     * @Assert\Valid()
     *
     * @var \IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity
     */
    private $target;

    /**
     * Retrieve the source
     *
     * @return \IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Define the source
     *
     * @param \IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity $source
     */
    public function setSource(Entity $source = null)
    {
        $this->source = $source;
    }

    /**
     * Retrieve the target
     *
     * @return \IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Define the target
     *
     * @param \IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity $target
     */
    public function setTarget(Entity $target = null)
    {
        $this->target = $target;
    }
}
