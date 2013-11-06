<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Renderer;

use IC\Bundle\Base\ComponentBundle\Resolver\RendererResolver;

/**
 * Array Renderer
 *
 * @author Yuan Xie <shayx@nationalfibre.net>
 */
class ArrayRenderer extends Renderer
{
    /**
     * @var array
     */
    protected $fieldList = array();

    /**
     * {@inheritdoc}
     */
    protected function getDefaultRenderingOption()
    {
        return self::RENDER_SHOW;
    }

    /**
     * Retrieve property's value
     *
     * @param string $property Property name
     * @param mixed  $args     Ignored
     *
     * @return mixed
     *
     * {@internal This is required by Twig. }}
     */
    public function __call($property, $args)
    {
        if ( ! isset($this->fieldList[$property])) {
            $fieldList          = $this->getAttribute('fieldList');
            $fieldAttributeList = isset($fieldList[$property]) ? $fieldList[$property] : array();

            $value = $this->value[$property];

            $rendererClass = RendererResolver::getRendererClass($value);

            $this->fieldList[$property] = new $rendererClass($value, $fieldAttributeList);
        }

        return $this->fieldList[$property];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return json_encode(\Doctrine\Common\Util\Debug::export($this->value, 4));
    }
}
