<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Tools;

use IC\Bundle\Base\TestBundle\Test\TestCase;

use IC\Bundle\Base\ComponentBundle\Tools\SimpleXmlElement;

/**
 * Test for SimpleXMlElement
 *
 * @group Unit
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class SimpleXmlElementTest extends TestCase
{
    /**
     * Should add a CDATA Child in the XML
     *
     * @param array $data xmlDataProvider
     *
     * @dataProvider xmlDataProvider

     */
    public function testShouldAddaChildCData($data)
    {
        $expectedXml = new SimpleXmlElement("<fields><field><![CDATA[{$data}]]></field></fields>");
        $xml         = new SimpleXmlElement("<fields></fields>");

        $xml->addChildCData('field', $data);
        $this->assertEquals($expectedXml->asXML(), $xml->asXML());
    }

    /**
     * Data provider
     *
     * @return array $data;
     */
    public function xmlDataProvider()
    {
        $data = array_fill(1, 36, array(mt_rand()));

        return $data;
    }
}
