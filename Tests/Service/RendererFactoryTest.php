<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Base\ComponentBundle\Service\RendererFactory;

/**
 * Test cases for RendererFactory.
 *
 * @group Factory
 * @group Service
 * @group Unit
 *
 * @author Enzo Rizzo <enzor@nationalfibre.net>
 *
 */

class RendererFactoryTest extends TestCase
{
    /**
     * Setup test - create new instance of RendererFactory
     */
    protected function setUp()
    {
        $this->factory = new RendererFactory();
    }

    /**
     * Test with any types
     *
     * @param array $data
     * @param array $expectedResult
     *
     * @dataProvider dataProviderForGetRenderer
     *
     */
    public function testGetRendererWithPrimitiveTypesReturnsExpectedRenderer($data, $expectedResult)
    {
        $result = $this->factory->getRenderer($data);

        $this->assertInstanceOf($expectedResult, $result);
    }

    /**
     * Provide data for test getRendererClass.
     *
     * @return array
     */
    public function dataProviderForGetRenderer()
    {
        return array(
            array(
                array(),
                '\IC\Bundle\Base\ComponentBundle\Renderer\ArrayRenderer',
            ),
            array(
                new \stdClass,
                '\IC\Bundle\Base\ComponentBundle\Renderer\StdClassRenderer',
            ),
            array(
                'a_string',
                '\IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer',
            ),
            array(
                5,
                '\IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer',
            ),
            array(
                new \DateTime,
                '\IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer',
            ),
            array(
                new ArrayCollection(),
                '\IC\Bundle\Base\ComponentBundle\Renderer\CollectionRenderer'
            )
        );
    }

    /**
     * Test getRenderer with some value parameter
     */
    public function testRendererWithValue()
    {
        $data   = array('value' => 'mock value');
        $result = $this->factory->getRenderer($data);

        $this->assertEquals('mock value', $result->getValue());
    }
}
