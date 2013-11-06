<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Service;

use IC\Bundle\Base\ComponentBundle\Resolver\RendererResolver;

/**
 * ComponentRendererFactory
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class RendererFactory
{
    /**
     * Retrieve a renderer.
     *
     * @param mixed $data the data for the creation of the corresponding renderer
     *
     * @return \IC\Bundle\Base\ComponentBundle\Renderer\AbstractRenderer
     */
    public function getRenderer($data)
    {
        $value      = $data;
        $attributes = array();

        if (is_array($data) && isset($data['value'])) {
            $value      = $data['value'];
            $attributes = $data;

            unset($attributes['value']);
        }

        $rendererClass = RendererResolver::getRendererClass($value);

        return new $rendererClass($value, $attributes);
    }
}
