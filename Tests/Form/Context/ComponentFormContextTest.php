<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Form\Context;

use IC\Bundle\Base\ComponentBundle\Form\Context\ComponentFormContext;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Unit tests for the ComponentFormContext class
 *
 * @group Form
 * @group Unit
 *
 * @author David Maignan <davidm@nationalfibre.net>
 */
class ComponentFormContextTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\Form\Context\ComponentFormContext
     */
    private $componentFormContext;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Test set, get & has method
     */
    public function testSetGetHas()
    {
        $this->componentFormContext = new ComponentFormContext();

        $objectA = new \stdClass();
        $objectA->label = 'mock label A';

        $this->componentFormContext->set('object_a', $objectA);

        $this->assertEquals($objectA, $this->componentFormContext->get('object_a'));
        $this->assertTrue($this->componentFormContext->has('object_a'));
        $this->assertFalse($this->componentFormContext->has('object_b'));
        $this->assertEquals(null, $this->componentFormContext->get('object_b'));
    }
}
