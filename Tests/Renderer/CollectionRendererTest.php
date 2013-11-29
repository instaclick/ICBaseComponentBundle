<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Renderer;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Base\ComponentBundle\Renderer\CollectionRenderer;
use IC\Bundle\Base\ComponentBundle\Tests\MockObject\GenericModel;

/**
 * Collection Renderer Test
 *
 * @group ICBaseComponent
 * @group Renderer
 * @group Unit
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 * @author John Zhang <johnz@nationalfibre.net>
 */
class CollectionRendererTest extends TestCase
{
    /**
     * test getDefaultRenderingOption
     */
    public function testGetDefaultRenderingOption()
    {
        $renderer = new CollectionRenderer(null);

        $this->assertEquals($renderer->getAttribute('render'), 'show');
    }

    /**
     * test attribute getter (if found)
     */
    public function testAttributeGetterIfFound()
    {
        $renderer = new CollectionRenderer(null, array('a' => 1));

        $this->assertEquals($renderer->getAttribute('a'), 1);
    }

    /**
     * test attribute getter (if not found)
     */
    public function testAttributeGetterIfNotFound()
    {
        $renderer = new CollectionRenderer(null, array('a' => 1));

        $this->assertNull($renderer->getAttribute('b'));
    }

    /**
     * test item getter
     */
    public function testItemGetter()
    {
        $model = new GenericModel();

        $model->setKey('author');
        $model->addMetadata('foo');
        $model->addMetadata('bar');

        $collection = new ArrayCollection;

        $collection->add($model);

        $collectionRenderer = new CollectionRenderer($collection);

        foreach ($collectionRenderer as $renderer) {
            $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\EntityRenderer', $renderer);
            $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $renderer->key());
            $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $renderer->marked());
            $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\CollectionRenderer', $renderer->metadata());
        }
    }

    /**
     * test collection with nested getter
     */
    public function testCollectionWithNestedNodes()
    {
        $subModel = new GenericModel();
        $subModel->setKey('author');
        $subModel->setContent('instaclick');

        $model = new GenericModel();
        $model->setKey('book');
        $model->setContent($subModel);

        $collection = new ArrayCollection;
        $collection->add($model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
                'content' => array(
                    'fieldList' => array(
                        'key'     => array('render'   => 'show'),
                        'content' => array('location' => 'toronto')
                    )
                )
            )
        ));

        $renderer = $collectionRenderer->first();

        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer', $renderer->key());
        $this->assertInstanceOf('IC\Bundle\Base\ComponentBundle\Renderer\EntityRenderer', $renderer->content());
        $this->assertTrue($renderer->content()->key()->isRenderable());
        $this->assertEquals('author', $renderer->content()->key());
        $this->assertEquals('toronto', $renderer->content()->content()->getAttribute('location'));
    }

    /**
     * Test __toString()
     */
    public function testToString()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->add($model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $this->assertEquals('[{"__CLASS__":"IC\\\Bundle\\\Base\\\ComponentBundle\\\Tests\\\MockObject\\\GenericModel","key":"book","content":null,"marked":false,"metadata":[]}]', (string) $collectionRenderer);
    }

    /**
     * Test add method
     *
     * @expectedException \RuntimeException
     */
    public function testAdd()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->add($model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $collectionRenderer->add(new \stdClass());
    }

    /**
     * Test clear method
     *
     * @expectedException \RuntimeException
     */
    public function testClear()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->add($model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $collectionRenderer->clear();
    }

    /**
     * Test contains method
     */
    public function testContains()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->add($model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $result = $collectionRenderer->contains($model);

        $this->assertTrue($result);
    }

    /**
     * Test isEmpty method
     */
    public function testIsEmpty()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->add($model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $this->assertFalse($collectionRenderer->isEmpty());
    }

    /**
     * Test remove
     *
     * @expectedException \RuntimeException
     */
    public function testRemove()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->add($model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $collectionRenderer->remove('book');
    }

    /**
     * Test removeElement
     *
     * @expectedException \RuntimeException
     */
    public function testRemoveElement()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->add($model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $collectionRenderer->removeElement('book');
    }

    /**
     * Test containsKey method
     */
    public function testContainsKey()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->set('mock_key', $model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $this->assertTrue($collectionRenderer->containsKey('mock_key'));
        $this->assertFalse($collectionRenderer->containsKey('does_not_exists'));
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $target = new ArrayCollection();
        $target->set('shelf', 'book');

        $collection = new ArrayCollection();
        $collection->set('mock_key', $target);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'mock_key' => array('render' => 'show'),
            )
        ));

        $expected = new CollectionRenderer($target, array(
            'fieldList' => array(
                'mock_key' => array('render' => 'show'),
            )
        ));

        $result  = $collectionRenderer->get('mock_key');
        $result2 = $collectionRenderer->get('mock_key');

        $this->assertEquals($expected, $result);
        $this->assertEquals($expected, $result2);
    }

    /**
     * Test getKeys method
     */
    public function testGetKeys()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->set('mock_key', $model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'mock_key' => array('render' => 'show'),
            )
        ));

        $keys = $collectionRenderer->getKeys();
        $this->assertEquals(array('mock_key'), $keys);
    }

    /**
     * Test getValues method
     */
    public function testGetValues()
    {
        $target = new ArrayCollection();
        $target->set('shelf', 'book');

        $collection = new ArrayCollection();
        $collection->set('mock_key', $target);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'mock_key' => array('render' => 'show'),
            )
        ));

        $expected = array(
            'mock_key' => new CollectionRenderer(
                $target,
                array(
                    'fieldList' => array(
                        'mock_key' => array('render' => 'show'),
                    )
                )
            )
        );

        $values  = $collectionRenderer->getValues();
        $values2 = $collectionRenderer->getValues();

        $this->assertEquals($expected, $values);
        $this->assertEquals($expected, $values2);
    }

    /**
     * Test set
     *
     * @expectedException \RuntimeException
     */
    public function testSet()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->set('mock_key', $model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $collectionRenderer->set('mock_key', new GenericModel());
    }

    /**
     * Test toArray
     */
    public function testToArray()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->set('mock_key', $model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $result = $collectionRenderer->toArray();

        $expected = array('mock_key' => $model);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test last
     */
    public function testLast()
    {
        $target = new ArrayCollection();
        $target->set('shelf', 'book');

        $collection = new ArrayCollection();
        $collection->set('mock_key', $target);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key' => array('render' => 'show'),
            )
        ));

        $expected = new CollectionRenderer($target, array(
            'fieldList' => array(
                'key' => array('render' => 'show'),
            )
        ));

        $result = $collectionRenderer->last();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test key method
     */
    public function testKey()
    {
        $model = new GenericModel();
        $model->setKey('book');

        $collection = new ArrayCollection;
        $collection->set('mock_key', $model);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key'     => array('render' => 'hide'),
            )
        ));

        $key = $collectionRenderer->key();

        $this->assertEquals('mock_key', $key);
    }

    /**
     * Test current
     */
    public function testCurrent()
    {
        $target = new ArrayCollection();
        $target->set('shelf', 'book');

        $collection = new ArrayCollection();
        $collection->set('mock_key', $target);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key' => array('render' => 'show'),
            )
        ));

        $expected = new CollectionRenderer($target, array(
            'fieldList' => array(
                'key' => array('render' => 'show'),
            )
        ));

        $result = $collectionRenderer->current();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test next
     */
    public function testNext()
    {
        $target  = new ArrayCollection();
        $target2 = new ArrayCollection();

        $target->set('shelf', 'book');
        $target2->set('shelf2', 'book2');

        $collection = new ArrayCollection();
        $collection->set('mock_key', $target);
        $collection->set('mock_key2', $target2);

        $collectionRenderer = new CollectionRenderer($collection, array(
            'fieldList' => array(
                'key' => array('render' => 'show'),
            )
        ));

        $result = $collectionRenderer->next();

        $this->assertEquals($target2, $result);
    }

    /**
     * Test exists
     *
     * @param mixed $element  Element to build the render.
     * @param mixed $expected Expected result.
     *
     * @dataProvider providerDataForTestExists
     */
    public function testExists($element, $expected)
    {
        $collectionRenderer = new CollectionRenderer($element, array());
        $closure            = function () {
            return true;
        };

        $this->assertEquals($expected, $collectionRenderer->exists($closure));
    }

    /**
     * Provide data for testExists
     *
     * @return array
     */
    public function providerDataForTestExists()
    {
        return array(
            array(
                new ArrayCollection(array('a' => 1)),
                true
            ),
            array(
                new ArrayCollection(),
                false
            ),
        );
    }

    /**
     * Test filter
     *
     * @param mixed   $element  Element to build the render.
     * @param Closure $closure  Closure for filter.
     * @param mixed   $expected Expected result.
     *
     * @dataProvider providerDataForTestFilter
     */
    public function testFilter($element, $closure, $expected)
    {
        $collectionRenderer = new CollectionRenderer($element, array());

        $this->assertEquals($expected, $collectionRenderer->filter($closure));
    }

    /**
     * Provide data for testFilter
     *
     * @return array
     */
    public function providerDataForTestFilter()
    {
        $nonEmptyCollection = new ArrayCollection(array('a' => 1));
        $emptyCollection    = new ArrayCollection();

        return array(
            array(
                $nonEmptyCollection,
                function () {
                    return true;
                },
                $nonEmptyCollection,
            ),
            array(
                new ArrayCollection(),
                function () {
                    return true;
                },
                $emptyCollection,
            ),
            array(
                $nonEmptyCollection,
                function () {
                    return false;
                },
                $emptyCollection,
            ),
        );
    }
}
