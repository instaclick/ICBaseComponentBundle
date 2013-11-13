<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Bundle\Base\ComponentBundle\Tests\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\ComponentBundle\Form\DataTransformer\EntityToEntityIdTransformer;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Unit tests for EntityToEntityIdTransformer class
 *
 * @group Form
 * @group Unit
 *
 * @author David Maignan <davidm@nationalfibre.net>
 */
class EntityToEntityIdTransformerTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository
     */
    private $entityRepository;

    /**
     * @var \IC\Bundle\Base\ComponentBundle\Form\DataTransformer\EntityToEntityIdTransformer
     */
    private $transformer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->entityRepository = $this->createMock('IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository');
    }

    /**
     * Test missing argument constructor
     *
     * @expectedException \Exception
     */
    public function testConstructorMissingArgumentEntityRepository()
    {
        $this->transformer = new EntityToEntityIdTransformer();
    }

    /**
     * Test missing argument constructor
     *
     * @expectedException \Exception
     */
    public function testConstructorMissingArgumentProperty()
    {
        $this->transformer = new EntityToEntityIdTransformer($this->entityRepository);
    }

    /**
     * Test missing argument constructor
     *
     * @expectedException \Exception
     */
    public function testConstructorMissingArgumentDefaultId()
    {
        $this->transformer = new EntityToEntityIdTransformer($this->entityRepository, 'mockProperty');
    }

    /**
     * Test transform without and entity
     *
     * @param string|null $defaultId
     *
     * @dataProvider dataProviderTransformWithNoEntity
     */
    public function testTransformWithNoEntity($defaultId)
    {
        $this->transformer = new EntityToEntityIdTransformer($this->entityRepository, 'mock_property', $defaultId);

        $result = $this->transformer->transform(null);

        $this->assertEquals($defaultId, $result);
    }

    /**
     * Data provider for testTransformWithNoEntity
     *
     * @return array
     */
    public function dataProviderTransformWithNoEntity()
    {
        $data = array(
            array('defaultId' => 'id'),
            array('defaultId' => 'label'),
            array('defaultId' => null)
        );

        return $data;
    }

    /**
     * Test transform method
     *
     * @param string $property
     * @param mixed  $value
     *
     * @dataProvider dataProviderTransform
     */
    public function testTransform($property, $value)
    {
        $helper = $this->getHelper('Unit\Entity');
        $user   = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\User', 1);

        $this->transformer = new EntityToEntityIdTransformer($this->entityRepository, $property, 'mockId');

        $result = $this->transformer->transform($user);

        $this->assertEquals($value, $result);
    }

    /**
     * Data provider for testTransform
     *
     * @return array
     */
    public function dataProviderTransform()
    {
        $data = array();

        $data[] = array(
            'property' => 'screenName',
            'value'    => 'mock screenName'
        );

        return $data;
    }

    /**
     * Test reverseTransform without an identifier
     */
    public function testReverseTransformWithoutIdentifier()
    {
        $this->transformer = new EntityToEntityIdTransformer($this->entityRepository, 'mockProperty', 'mockDefaultId');

        $result = $this->transformer->reverseTransform(null);

        $this->assertEquals(null, $result);
    }

    /**
     * Test reverse transform
     */
    public function testReverseTransform()
    {
        $this->transformer = new EntityToEntityIdTransformer($this->entityRepository, 'mockProperty', 'mockDefaultId');

        $helper   = $this->getHelper('Unit\Entity');
        $entity   = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 1);
        $criteria = $this->createMock('IC\Bundle\Base\ComponentBundle\Entity\Filter\Criteria');

        $this->entityRepository->expects($this->once())
            ->method('newCriteria')
            ->will($this->returnValue($criteria));

        $this->entityRepository->expects($this->once())
            ->method('filter')
            ->with($criteria)
            ->will($this->returnValue($entity));

        $result = $this->transformer->reverseTransform(1);

        $this->assertEquals($entity, $result);
    }

    /**
     * Test reverse transform
     *
     * @expectedException       \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionCode    500
     * @expectedExceptionMessage An entity with id "2" does not exist.
     */
    public function testReverseTransformWithNoEntityReturned()
    {
        $this->transformer = new EntityToEntityIdTransformer($this->entityRepository, 'mockProperty', 'mockDefaultId');
        $criteria          = $this->createMock('IC\Bundle\Base\ComponentBundle\Entity\Filter\Criteria');

        $this->entityRepository->expects($this->once())
            ->method('newCriteria')
            ->will($this->returnValue($criteria));

        $this->entityRepository->expects($this->once())
            ->method('filter')
            ->with($criteria)
            ->will($this->returnValue(null));

        $this->transformer->reverseTransform(2);
    }
}
