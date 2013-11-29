<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Validator\Constraints;

use IC\Bundle\Base\ComponentBundle\Validator\Constraints\EntityNotIdentical;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Entity Not Identical Constraint test
 *
 * @group ICBaseComponentBundle
 * @group Unit
 *
 * @author Enzo Rizzo <enzor@nationalfibre.net>
 */
class EntityNotIdenticalTest extends TestCase
{
    /**
     *
     */
    private $entityNotIdentical;

    /**
     * Test the returning strings
     */
    public function testEntityNotIdentical()
    {
        $this->entityNotIdentical = new EntityNotIdentical();

        $this->assertEquals('ic_base_component.validator.entity_not_identical', $this->entityNotIdentical->validatedBy());
        $this->assertEquals('ic_base_component.validator.error_message.entity_not_identical', $this->entityNotIdentical->message);
        $this->assertEquals('class', $this->entityNotIdentical->getTargets());
    }
}
