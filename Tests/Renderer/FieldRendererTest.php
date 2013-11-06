<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Renderer;

use IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Field Renderer Test
 *
 * @group ICBaseComponent
 * @group Renderer
 * @group Unit
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class FieldRendererTest extends TestCase
{
    /**
     * test attribute getter (if found)
     */
    public function testAttributeGetterIfFound()
    {
        $r = new FieldRenderer(null, array('a' => 1));
        $this->assertEquals($r->getAttribute('a'), 1);
    }

    /**
     * test attribute getter (if not found)
     */
    public function testAttributeGetterIfNotFound()
    {
        $r = new FieldRenderer(null, array('a' => 1));
        $this->assertNull($r->getAttribute('b'));
    }

    /**
     * test auto mode with valid value
     */
    public function testAutoModeWithValidValue()
    {
        $r = new FieldRenderer(1);
        $this->assertTrue($r->isRenderable());
    }

    /**
     * test auto mode with array value
     */
    public function testAutoModeWithArrayValue()
    {
        $r = new FieldRenderer(array('book' => 'spring framework', 'language' => 'java'));
        $this->assertTrue($r->isRenderable());
    }

    /**
     * test auto mode with invalid value
     */
    public function testAutoModeWithRenderableCollectionValue()
    {
        $c = new \Doctrine\Common\Collections\ArrayCollection();
        $c[] = 'a';
        $c[] = 'b';

        $r = new FieldRenderer($c);
        $this->assertTrue($r->isRenderable());
    }

    /**
     * test auto mode with true boolean value
     */
    public function testAutoModeWithTrueBooleanValue()
    {
        $r = new FieldRenderer(true);
        $this->assertTrue($r->isRenderable());
    }

    /**
     * test auto mode with false boolean value
     */
    public function testAutoModeWithFalseBooleanValue()
    {
        $r = new FieldRenderer(false);
        $this->assertTrue($r->isRenderable());
    }

    /**
     * test show mode with valid value
     */
    public function testShowModeWithValidValue()
    {
        $r = new FieldRenderer(1, array('render' => 'show'));
        $this->assertTrue($r->isRenderable());
    }

    /**
     * test show mode with invalid value
     */
    public function testShowModeWithInvalidValue()
    {
        $r = new FieldRenderer(null, array('render' => 'show'));
        $this->assertTrue($r->isRenderable());
    }

    /**
     * test hide mode with valid value
     */
    public function testHideModeWithValidValue()
    {
        $r = new FieldRenderer(1, array('render' => 'hide'));
        $this->assertFalse($r->isRenderable());
    }

    /**
     * test hide mode with invalid value
     */
    public function testHideModeWithInvalidValue()
    {
        $r = new FieldRenderer(null, array('render' => 'hide'));
        $this->assertFalse($r->isRenderable());
    }

    /**
     * test with default attribute assignment
     */
    public function testWithDefaultAttributeAssignment()
    {
        $r = new FieldRenderer(1, 'hide');
        $this->assertFalse($r->isRenderable());
    }
}
