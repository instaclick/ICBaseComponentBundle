<?php

/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Renderer;

/**
 * Abstract Renderer
 *
 * Please note that this class has ONE abstract method.
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 * @author Oleksii Strutsynskyi <oleksiis@nationalfibre.net>
 */
abstract class Renderer
{
    const RENDER_SHOW = 'show';
    const RENDER_HIDE = 'hide';
    const RENDER_AUTO = 'auto';

    /**
     * @var mixed data value used for rendering which can be either primitive or object.
     */
    protected $value;

    /**
     * @var array attributes used for rendering
     */
    protected $attributeList;

    /**
     * Constructor
     *
     * @param mixed             $value         Value
     * @param array|string|null $attributeList Attribute list
     */
    public function __construct($value, $attributeList = array())
    {
        $this->value         = $value;
        $this->attributeList = $attributeList;
    }

    /**
     * Retrieve the value to render.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Retrieve an attribute.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getAttribute($name)
    {
        // Deal with the shortcut attribute.
        if (empty($this->attributeList) || is_string($this->attributeList)) {
            $this->attributeList = array(
                'render' => empty($this->attributeList)
                    ? $this->getDefaultRenderingOption()
                    : $this->attributeList
            );
        }

        return isset($this->attributeList[$name])
            ? $this->attributeList[$name]
            : null;
    }

    /**
     * Retrieve an attribute.
     *
     * Please note that it can be either an array or string.
     *
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributeList;
    }

    /**
     * Test if this renderer is renderable/usable.
     *
     * @return boolean
     *
     * @throws \RuntimeException when the rendering option is not recognized
     */
    public function isRenderable()
    {
        $renderingOption = $this->getAttribute('render');

        switch ($renderingOption) {
            case self::RENDER_SHOW:
                return true;
            case self::RENDER_HIDE:
                return false;
        }

        throw new \RuntimeException('The rendering option is not recognized. Given "' . $renderingOption .'".');
    }

    /**
     * Convert this renderer to string.
     *
     * @return string
     */
    public function __toString()
    {
        return (is_null($this->value) || is_bool($this->value)) ? '' : $this->value;
    }

    /**
     * Retrieve the default rendering option.
     *
     * @return string
     */
    abstract protected function getDefaultRenderingOption();
}
