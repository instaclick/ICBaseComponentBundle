<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Resolver;

use Doctrine\Common\Collections\Collection;
use IC\Bundle\Base\ComponentBundle\Exception\ResolverException;
use IC\Bundle\Base\ComponentBundle\Renderer\Renderer;
use IC\Bundle\Base\ComponentBundle\Entity\Entity;

/**
 * Renderer Resolver to determine the type of renderers
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 * @author Yuan Xie <shayx@nationalfibre.net>
 */
class RendererResolver
{
    /**
     * Determine the type of the renderer based
     *
     * @param mixed $value
     *
     * @return string the fully qualified class name.
     *
     * @throws \IC\Bundle\Base\ComponentBundle\Exception\ResolverException when the renderer is given
     */
    public static function getRendererClass($value)
    {
        switch (true) {
            case $value instanceof Renderer:
                throw new ResolverException('This value is already a renderer.');
            case is_array($value):
                $rendererClass = 'ArrayRenderer';
                break;
            case $value instanceof \stdClass:
                $rendererClass = 'StdClassRenderer';
                break;
            case $value instanceof Collection:
                $rendererClass = 'CollectionRenderer';
                break;
            case $value instanceof Entity || is_object($value) && ! $value instanceof \DateTime:
                $rendererClass = 'EntityRenderer';
                break;
            default:
                $rendererClass = 'FieldRenderer';
        }

        $rendererNamespace = preg_replace('/Resolver$/', 'Renderer', __NAMESPACE__);

        return '\\' . $rendererNamespace . '\\' . $rendererClass;
    }
}
