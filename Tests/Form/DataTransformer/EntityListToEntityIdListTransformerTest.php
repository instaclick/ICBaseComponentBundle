<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Bundle\Base\ComponentBundle\Tests\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository;
use IC\Bundle\Base\ComponentBundle\Form\DataTransformer\EntityListToEntityIdListTransformer;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Unit tests for the EntityListToEntityIdListTransformer class
 *
 * @group Form
 * @group Unit
 *
 * @author David Maignan <davidm@nationalfibre.net>
 */
class EntityListToEntityIdListTransformerTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\Form\DataTransformer\EntityListToEntityIdListTransformer
     */
    private $transformer;

    /**
     * @var \IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository
     */
    private $entityRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->entityRepository = $this->createMock('IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository');
    }

    /**
     * Test tranform with an empty entity list
     *
     * @param array|null $entityList List of entities
     *
     * @dataProvider dataProviderTransformEmptyEntityList
     */
    public function testTransformWithEmptyEntityList($entityList)
    {
        $this->transformer = new EntityListToEntityIdListTransformer($this->entityRepository);

        $result = $this->transformer->transform($entityList);

        $this->assertEmpty($result);
    }

    /**
     * Dataprovider for testTranformWithEmptyEntityList
     *
     * @return array
     */
    public function dataProviderTransformEmptyEntityList()
    {
        $data = array(
            array(null),
            array(array())
        );

        return $data;
    }

    /**
     * Test transform entity list
     */
    public function testTransformEntityList()
    {
        $helper  = $this->getHelper('Unit\Entity');
        $entityA = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 1);
        $entityB = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 2);
        $entityC = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 3);
        $entityD = null;

        $entityList = array($entityA, $entityB, $entityC, $entityD);

        $this->transformer = new EntityListToEntityIdListTransformer($this->entityRepository);

        $result = $this->transformer->transform($entityList);

        $this->assertEquals(array(1,2,3, null), $result);
    }

    /**
     * Test reverse transform with an empty entityList
     */
    public function testReverseTransformWithEmptyEntityList()
    {
        $this->transformer = new EntityListToEntityIdListTransformer($this->entityRepository);
        $result            = $this->transformer->reverseTransform(array());

        $this->assertEmpty($result);
    }

    /**
     * Test reverse transform with a named criteria
     */
    public function testReverseTransformWithNamedCriteria()
    {
        $entityIdList      = array(1, 6, 12, 33);
        $criteria          = 'mockColumnName';
        $namedCriteria     = $this->createMock('IC\Bundle\Base\ComponentBundle\Entity\Filter\NamedCriteria');
        $this->transformer = new EntityListToEntityIdListTransformer($this->entityRepository, $criteria);

        //Mock 4 entities
        $helper  = $this->getHelper('Unit\Entity');
        $entityA = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 1);
        $entityB = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 6);
        $entityC = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 12);
        $entityD = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 33);

        $this->entityRepository->expects($this->once())
            ->method('newNamedCriteria')
            ->with($criteria)
            ->will($this->returnValue($namedCriteria));

        $namedCriteria->expects($this->once())
            ->method('setParameter')
            ->with('entityList', $entityIdList, Connection::PARAM_INT_ARRAY);

        $this->entityRepository->expects($this->once())
            ->method('filter')
            ->with($namedCriteria)
            ->will($this->returnValue(new ArrayCollection(array($entityA, $entityB, $entityC, $entityD))));

        $result = $this->transformer->reverseTransform($entityIdList);

        $this->assertEquals(array(1 => $entityA, 6 => $entityB, 12 => $entityC, 33 => $entityD), $result);
    }

    /**
     * Test reverse transform without a named criteria
     */
    public function testReverseTransformWithoutNamedCriteria()
    {
        $entityIdList      = array(1002, 2001);
        $namedCriteria     = $this->createMock('IC\Bundle\Base\ComponentBundle\Entity\Filter\NamedCriteria');
        $queryBuilder      = $this->createMock('Doctrine\DBAL\Query\QueryBuilder');
        $this->transformer = new EntityListToEntityIdListTransformer($this->entityRepository);

        //Mock 2 entities
        $helper  = $this->getHelper('Unit\Entity');
        $entityA = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 1002);
        $entityB = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 2001);

        $this->entityRepository->expects($this->once())
            ->method('newCriteria')
            ->will($this->returnValue($queryBuilder));

        $queryBuilder->expects($this->once())
            ->method('where')
            ->will($this->returnValue($namedCriteria));

        $namedCriteria->expects($this->once())
            ->method('setParameter')
            ->with('entityList', $entityIdList, Connection::PARAM_INT_ARRAY);

        $this->entityRepository->expects($this->once())
            ->method('filter')
            ->with($namedCriteria)
            ->will($this->returnValue(new ArrayCollection(array($entityA, $entityB))));

        $result = $this->transformer->reverseTransform($entityIdList);

        $this->assertEquals(array(1002 => $entityA, 2001 => $entityB), $result);
    }

    /**
     * Test reverse transform with a wrong entity id
     *
     * @expectedException        \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage "" entity with id "35" does not exist.
     * @expectedExceptionCode    500
     */
    public function testReverseTransformWithWrongEntityId()
    {
        $entityIdList      = array(1, 6, 12, 35);
        $criteria          = 'mockColumnName';
        $namedCriteria     = $this->createMock('IC\Bundle\Base\ComponentBundle\Entity\Filter\NamedCriteria');
        $this->transformer = new EntityListToEntityIdListTransformer($this->entityRepository, $criteria);

        //Mock 4 entities
        $helper  = $this->getHelper('Unit\Entity');
        $entityA = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 1);
        $entityB = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 6);
        $entityC = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 12);
        $entityD = $helper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', 33);

        $this->entityRepository->expects($this->once())
            ->method('newNamedCriteria')
            ->with($criteria)
            ->will($this->returnValue($namedCriteria));

        $namedCriteria->expects($this->once())
            ->method('setParameter')
            ->with('entityList', $entityIdList, Connection::PARAM_INT_ARRAY);

        $this->entityRepository->expects($this->once())
            ->method('filter')
            ->with($namedCriteria)
            ->will($this->returnValue(new ArrayCollection(array($entityA, $entityB, $entityC, $entityD))));

        $result = $this->transformer->reverseTransform($entityIdList);

        $this->assertEquals(array(1 => $entityA, 6 => $entityB, 12 => $entityC, 33 => $entityD), $result);
    }
}
