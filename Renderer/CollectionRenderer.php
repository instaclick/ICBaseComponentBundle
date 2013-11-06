<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Renderer;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use IC\Bundle\Base\ComponentBundle\Resolver\RendererResolver;

/**
 * Collection Renderer
 *
 * Please note that:
 * 1. This class is intended to be read-only.
 * 2. This class is buggy as a very small number of its methods are actually used.
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class CollectionRenderer extends Renderer implements Collection
{
    /**
     * @var array the list of instantiated renderers
     */
    private $fieldList = array();

    /**
     * {@inheritdoc}
     */
    protected function getDefaultRenderingOption()
    {
        return self::RENDER_SHOW;
    }

    /**
     * Make the renderer.
     *
     * @param mixed $element
     *
     * @return \IC\Bundle\Base\ComponentBundle\Renderer\Renderer
     */
    protected function makeRenderer($element)
    {
        // For the collection, each item (direct descendant) has the same set of attributes as the collection.
        $attributes    = $this->getAttributes();
        $rendererClass = RendererResolver::getRendererClass($element);

        return new $rendererClass($element, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return json_encode(\Doctrine\Common\Util\Debug::export($this->value, 4));
    }

    /**
     * {@inheritdoc}
     */
    public function add($element)
    {
        throw new \RuntimeException("This method is not allowed from Collection Renderer");
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        throw new \RuntimeException("This method is not allowed from Collection Renderer");
    }

    /**
     * {@inheritdoc}
     */
    public function contains($element)
    {
        return $this->value->contains($element);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->value->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        throw new \RuntimeException("This method is not allowed from Collection Renderer");
    }

    /**
     * {@inheritdoc}
     */
    public function removeElement($element)
    {
        throw new \RuntimeException("This method is not allowed from Collection Renderer");
    }

    /**
     * {@inheritdoc}
     */
    public function containsKey($key)
    {
        return $this->value->containsKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if (isset($this->fieldList[$key])) {
            return $this->fieldList[$key];
        }

        $this->fieldList[$key] = $this->makeRenderer($this->value->get($key));

        return $this->fieldList[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys()
    {
        return $this->value->getKeys();
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        foreach ($this->value->getKeys() as $key) {
            if (isset($this->fieldList[$key])) {
                continue;
            }

            $this->fieldList[$key] = $this->makeRenderer($this->value->get($key));
        }

        return $this->fieldList;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        throw new \RuntimeException("This method is not allowed from Collection Renderer");
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->value->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function first()
    {
        return $this->makeRenderer($this->value->first());
    }

    /**
     * {@inheritdoc}
     */
    public function last()
    {
        return $this->makeRenderer($this->value->last());
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->value->key();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->makeRenderer($this->value->current());
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return $this->value->next();
    }

    /**
     * {@inheritdoc}
     */
    public function exists(Closure $p)
    {
        return $this->value->exists($p);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(Closure $p)
    {
        $itemList = $this->value->filter($p);

        $rendererList = new ArrayCollection;

        $count = count($itemList);

        for ($i = 0; $i < $count; $i++) {
            $rendererList[] = $this->makeRenderer($itemList);
        }

        return $this->value->filter($p);
    }

    /**
     * {@inheritdoc}
     */
    public function forAll(Closure $p)
    {
        return $this->value->forAll($p);
    }

    /**
     * {@inheritdoc}
     */
    public function map(Closure $func)
    {
        return $this->value->map($func);
    }

    /**
     * {@inheritdoc}
     */
    public function partition(Closure $p)
    {
        return $this->value->partition($p);
    }

    /**
     * {@inheritdoc}
     */
    public function indexOf($element)
    {
        return $this->value->indexOf($element);
    }

    /**
     * {@inheritdoc}
     */
    public function slice($offset, $length = null)
    {
        return $this->value->slice($offset, $length);
    }

    /**
     * Countable - Returns the number of elements in the collection.
     *
     * Implementation of the Countable interface.
     *
     * @return integer The number of elements in the collection.
     */
    public function count()
    {
        return $this->value->count();
    }

    /**
     * IteratorAggregate - Gets an iterator for iterating over the elements in the collection.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getValues());
    }

    /**
     * ArrayAccess implementation of offsetExists()
     *
     * @param mixed $offset
     *
     * @return boolean
     *
     * @see containsKey()
     */
    public function offsetExists($offset)
    {
        return $this->value->offsetExists($offset);
    }

    /**
     * ArrayAccess implementation of offsetGet()
     *
     * @param mixed $offset
     *
     * @return mixed
     *
     * @see get()
     */
    public function offsetGet($offset)
    {
        if (isset($this->fieldList[$offset])) {
            return $this->fieldList[$offset];
        }

        $this->fieldList[$offset] = $this->makeRenderer($this->value->offsetGet($offset));

        return $this->fieldList[$offset];
    }

    /**
     * ArrayAccess implementation of offsetSet()
     *
     * @param mixed $offset Offset
     * @param mixed $value  Value
     *
     * @return boolean
     *
     * @see add()
     * @see set()
     */
    public function offsetSet($offset, $value)
    {
        return $this->value->offsetSet($offset, $value);
    }

    /**
     * ArrayAccess implementation of offsetUnset()
     *
     * @param mixed $offset
     *
     * @return mixed
     *
     * @see remove()
     */
    public function offsetUnset($offset)
    {
        return $this->value->offsetUnset($offset);
    }
}
