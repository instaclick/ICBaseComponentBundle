<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Renderer;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\ComponentBundle\Renderer\ArrayRenderer;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Array Renderer Test
 *
 * @group ICBaseComponent
 * @group Renderer
 * @group Unit
 *
 * @author Yuan Xie <shayx@nationalfibre.net>
 */
class ArrayRendererTest extends TestCase
{
    /**
     * test attribute getter (if found)
     */
    public function testAttributeGetterIfFound()
    {
        $renderer = new ArrayRenderer(null, array('a' => 1));
        $this->assertEquals($renderer->getAttribute('a'), 1);
    }

    /**
     * test attribute getter (if not found)
     */
    public function testAttributeGetterIfNotFound()
    {
        $renderer = new ArrayRenderer(null, array('a' => 1));
        $this->assertNull($renderer->getAttribute('b'));
    }

    /**
     * test plain getter
     */
    public function testPlainGetter()
    {
        $model         = array();
        $model['key']  = 'author';
        $model['list'] = new ArrayCollection(array(
            'foo',
            'bar',
        ));

        $renderer = new ArrayRenderer($model);
        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $renderer->key());
        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\CollectionRenderer', $renderer->list());
    }

    /**
     * test nested getter
     */
    public function testNestedGetter()
    {
        $subModel            = array();
        $subModel['key']     = 'author';
        $subModel['content'] = 'instaclick';

        $model            = array();
        $model['key']     = 'book';
        $model['content'] = $subModel;

        $renderer = new ArrayRenderer($model, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
                'content' => array(
                    'fieldList' => array(
                        'key'     => array('render' => 'show'),
                        'content' => array('location' => 'toronto')
                    )
                )
            )
        ));

        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $renderer->key(), 'The GM key must be an field renderer.');
        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\ArrayRenderer', $renderer->content(), 'The GM content must be an entity renderer.');
        $this->assertTrue($renderer->content()->key()->isRenderable(), 'The key of the submodel should be renderable.');
        $this->assertEquals(
            $renderer->content()->content()->getAttribute('location'),
            'toronto',
            'The attribute of the content of the submodel should be passable from the parent.'
        );
    }

    /**
     * test nested getter with default attributes
     */
    public function testNestedGetterWithDefaultAttributes()
    {
        $subModel            = array();
        $subModel['key']     = 'author';
        $subModel['content'] = 'instaclick';

        $model            = array();
        $model['key']     = 'book';
        $model['content'] = $subModel;

        $renderer = new ArrayRenderer($model, array(
            'fieldList' => array(
                'key'     => 'hide',
                'content' => array(
                    'fieldList' => array(
                        'key'     => 'hide',
                        'content' => array('location' => 'toronto')
                    )
                )
            )
        ));

        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $renderer->key(), 'The GM key must be an field renderer.');
        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\ArrayRenderer', $renderer->content(), 'The GM content must be an entity renderer.');
        $this->assertFalse($renderer->content()->key()->isRenderable(), 'The key of the submodel should be renderable.');
        $this->assertEquals(
            $renderer->content()->content()->getAttribute('location'),
            'toronto',
            'The attribute of the content of the submodel should be passable from the parent.'
        );
    }

    /**
     * test nested getter on null pointer
     */
    public function testNestedGetterOnNullPointer()
    {
        $model        = array();
        $model['key'] = null;

        $renderer = new ArrayRenderer($model);

        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $renderer->key(), 'The GM key must be an field renderer.');
    }

    /**
     * test invalid getter
     *
     * @expectedException \PHPUnit_Framework_Error_Notice
     */
    public function testInvalidGetter()
    {
        $model           = array();
        $model['author'] = 'author';

        $renderer = new ArrayRenderer($model);
        $this->assertNull($renderer->echelon());
    }

    /**
     * Test __toString()
     */
    public function testToString()
    {
        $model            = array();
        $model['key']     = 'mock key';
        $model['content'] = 'mock content';

        $renderer = new ArrayRenderer($model);

        $this->assertEquals('{"key":"mock key","content":"mock content"}', (string) $renderer);
    }

    /**
     * Test get value
     */
    public function testGetValue()
    {
        $model            = array();
        $model['key']     = 'mock key';
        $model['content'] = 'mock content';

        $renderer = new ArrayRenderer($model);

        $result = $renderer->getValue();

        $this->assertEquals($model, $result);
    }

    /**
     * Test with invalid rendering option
     *
     * @expectedException \RuntimeException
     */
    public function testIsRenderableWithOptionNotRecognized()
    {
        $model            = array();
        $model['key']     = 'mock key';
        $model['content'] = 'mock content';

        $renderer = new ArrayRenderer($model, array(
            'fieldList' => array(
                'key' => 'foo'
            )
        ));

        $renderer->key()->isRenderable();
    }
}
