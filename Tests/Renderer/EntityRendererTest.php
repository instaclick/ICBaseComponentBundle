<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Renderer;

use IC\Bundle\Base\ComponentBundle\Renderer\EntityRenderer;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Base\ComponentBundle\Tests\MockObject\GenericModel;

/**
 * Entity Renderer Test
 *
 * @group ICBaseComponent
 * @group Renderer
 * @group Unit
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class EntityRendererTest extends TestCase
{
    /**
     * test attribute getter (if found)
     */
    public function testAttributeGetterIfFound()
    {
        $r = new EntityRenderer(null, array('a' => 1));
        $this->assertEquals($r->getAttribute('a'), 1);
    }

    /**
     * test attribute getter (if not found)
     */
    public function testAttributeGetterIfNotFound()
    {
        $r = new EntityRenderer(null, array('a' => 1));
        $this->assertNull($r->getAttribute('b'));
    }

    /**
     * test plain magic getter
     */
    public function testPlainMagicGetter()
    {
        $model = new GenericModel();
        $model->setKey('author');
        $model->addMetadata('foo');
        $model->addMetadata('bar');

        $r = new EntityRenderer($model);
        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $r->key());
        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $r->marked());
        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\CollectionRenderer', $r->metadata());
    }

    /**
     * test nested magic getter
     */
    public function testNestedMagicGetter()
    {
        $subModel = new GenericModel();
        $subModel->setKey('author');
        $subModel->setContent('instaclick');

        $model = new GenericModel();
        $model->setKey('book');
        $model->setContent($subModel);

        $r = new EntityRenderer($model, array(
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

        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $r->key(), 'The GM key must be an field renderer.');
        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\EntityRenderer', $r->content(), 'The GM content must be an entity renderer.');
        $this->assertTrue($r->content()->key()->isRenderable(), 'The key of the submodel should be renderable.');
        $this->assertEquals(
            $r->content()->content()->getAttribute('location'),
            'toronto',
            'The attribute of the content of the submodel should be passable from the parent.'
        );
    }

    /**
     * test nested magic getter with default attributes
     */
    public function testNestedMagicGetterWithDefaultAttributes()
    {
        $subModel = new GenericModel();
        $subModel->setKey('author');
        $subModel->setContent('instaclick');

        $model = new GenericModel();
        $model->setKey('book');
        $model->setContent($subModel);

        $r = new EntityRenderer($model, array(
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

        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $r->key(), 'The GM key must be an field renderer.');
        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\EntityRenderer', $r->content(), 'The GM content must be an entity renderer.');
        $this->assertFalse($r->content()->key()->isRenderable(), 'The key of the submodel should be renderable.');
        $this->assertEquals(
            $r->content()->content()->getAttribute('location'),
            'toronto',
            'The attribute of the content of the submodel should be passable from the parent.'
        );
    }

    /**
     * test nested magic getter on null pointer
     */
    public function testNestedMagicGetterOnNullPointer()
    {
        $model = new GenericModel();
        $model->setKey(null);

        $r = new EntityRenderer($model);

        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $r->key(), 'The GM key must be an field renderer.');
    }

    /**
     * test invalid magic getter
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidMagicGetter()
    {
        $model = new GenericModel();
        $model->setKey('author');

        $r = new EntityRenderer($model);
        $this->assertNull($r->echelon());
    }
}
