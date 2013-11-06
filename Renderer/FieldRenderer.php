<?php

/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Renderer;

/**
 * Field Renderer
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class FieldRenderer extends Renderer
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultRenderingOption()
    {
        return self::RENDER_AUTO;
    }

    /**
     * {@inheritdoc}
     */
    public function isRenderable()
    {
        if ($this->getAttribute('render') !== self::RENDER_AUTO) {
            return parent::isRenderable();
        }

        if (is_bool($this->value)) {
            return true;
        }

        return ( ! empty($this->value));
    }
}
