<?php

/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\MockObject;

/**
 * Generic Model only for testing.
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class GenericModel
{
    protected $key;
    protected $content;
    protected $marked = false;
    protected $metadata;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->metadata = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set key.
     *
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get content.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content.
     *
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Return the model's "marked" status.
     *
     * @return boolean
     */
    public function isMarked()
    {
        return $this->marked;
    }

    /**
     * Set the model's "marked" status.
     *
     * @param boolean $marked
     */
    public function setMarked($marked)
    {
        $this->marked = $marked;
    }

    /**
     * Get the metadata
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Add the metadata
     *
     * @param mixed $metadata
     */
    public function addMetadata($metadata)
    {
        $this->metadata[] = $metadata;
    }
}
