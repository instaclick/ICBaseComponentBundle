<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Base\ComponentBundle\Resolver\RendererResolver;
use IC\Bundle\Base\ComponentBundle\Renderer\FieldRenderer;

/**
 * Test cases for RendererResolver.
 *
 * @group Service
 * @group Unit
 *
 * @author Yuan Xie <shayx@nationalfibre.net>
 */
class RendererResolverTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\Resolver\RendererResolver
     */
    private $resolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resolver = new RendererResolver();
    }

    /**
     * Test getRendererClass
     *
     * @param mixed  $value          Value
     * @param string $expectedResult Expected result
     *
     * @dataProvider providerDataForTestGetRendererClass
     */
    public function testGetRendererClass($value, $expectedResult)
    {
        $result = $this->resolver->getRendererClass($value);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test getRendererClass throw Exception
     *
     * @expectedException \IC\Bundle\Base\ComponentBundle\Exception\ResolverException
     */
    public function testGetRendererClassThrowException()
    {
        $value = new FieldRenderer('a_string');

        $this->resolver->getRendererClass($value);
    }

    /**
     * Provide data for test getRendererClass
     *
     * @return array
     */
    public function providerDataForTestGetRendererClass()
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
                new ArrayCollection(array()),
                '\IC\Bundle\Base\ComponentBundle\Renderer\CollectionRenderer',
            ),
            array(
                $this->createMock('\IC\Bundle\Base\ComponentBundle\Entity\Entity'),
                '\IC\Bundle\Base\ComponentBundle\Renderer\EntityRenderer',
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
        );
    }
}
