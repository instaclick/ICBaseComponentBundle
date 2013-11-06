<?php

/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Renderer;

use IC\Bundle\Base\ComponentBundle\Resolver\RendererResolver;

/**
 * Entity Renderer
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 * @author Anthon Pang <anthonp@nationalfibre.net>
 */
class EntityRenderer extends Renderer
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
     * Determine getter
     *
     * @param string $property Property name
     *
     * @return string|null
     *
     * @throws \InvalidArgumentException
     */
    private function getGetter($property)
    {
        $prefixes = array('is', 'get', 'to');
        $suffix   = ucfirst($property);

        foreach ($prefixes as $prefix) {
            $methodName = $prefix . $suffix;

            if (method_exists($this->value, $methodName)) {
                return $methodName;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('Undefined property "%s" in entity "%s".', $property, get_class($this->value))
        );
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

            $getterMethod  = $this->getGetter($property);
            $value         = $this->value->$getterMethod();
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
