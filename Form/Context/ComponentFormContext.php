<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Context;

/**
 * Component Form Context
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 */
class ComponentFormContext implements FormContextInterface
{
    /**
     * @var array
     */
    private $contextList = array();

    /**
     * Define a new context entry
     *
     * @param string $id     ID
     * @param mixed  $object Object
     */
    public function set($id, $object)
    {
        $this->contextList[$id] = $object;
    }

    /**
     * Retrieve the context entry
     *
     * @param string $id
     *
     * @return mixed
     */
    public function get($id)
    {
        return isset($this->contextList[$id])
            ? $this->contextList[$id]
            : null;
    }

    /**
     * Check existence of a context entry
     *
     * @param string $id
     *
     * @return boolean
     */
    public function has($id)
    {
        return isset($this->contextList[$id]);
    }
}
