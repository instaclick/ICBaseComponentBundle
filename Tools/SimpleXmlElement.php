<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tools;

/**
 * SimpleXmlElement
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class SimpleXmlElement extends \SimpleXmlElement
{
    /**
     * Add CDATA text in a node
     *
     * @param string $cdata The CDATA value to add
     */
    private function addCData($cdata)
    {
        $node= dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata));
    }

    /**
     * Create a child with CDATA value
     *
     * @param string $name  The name of the child element to add.
     * @param string $cdata The CDATA value of the child element.
     *
     * @return SimpleXMLElement $child
     */
    public function addChildCData($name, $cdata)
    {
        $child = $this->addChild($name);
        $child->addCData($cdata);

        return $child;
    }
}
