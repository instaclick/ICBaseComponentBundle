<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Bundle\Base\ComponentBundle\Tests\Form\Service\Filter;

use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterService;

/**
 * Dependent Entity Form Filter Service Test
 *
 * @group ICBaseComponent
 * @group Service
 * @group Unit
 *
 * @author Oleksandr Kovalov <oleksandrk@nationalfibre.net>
 */
class DependentEntityFormFilterServiceTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterService
     */
    private $service;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManagerMock;

    /**
     * @var \IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository
     */
    private $repositoryMock;

    /**
     * @var \IC\Bundle\Base\ComponentBundle\Entity\Filter\Criteria
     */
    private $criteriaMock;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->service           = new DependentEntityFormFilterService();
        $this->entityManagerMock = $this->createMock('Doctrine\ORM\EntityManager');
        $this->repositoryMock    = $this->createMock('IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository');
        $this->criteriaMock      = $this->createMock('IC\Bundle\Base\ComponentBundle\Entity\Filter\Criteria');
    }

    /**
     * Test buildCriteria.
     *
     * @param string $value
     * @param array  $optionList
     *
     * @dataProvider dataProvider
     */
    public function testBuildCriteria($value, $optionList)
    {
        $this->entityManagerMock
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo($optionList['class']))
            ->will($this->returnValue($this->repositoryMock));

        $this->repositoryMock
            ->expects($this->once())
            ->method('newCriteria')
            ->will($this->returnValue($this->criteriaMock));

        $this->criteriaMock
            ->expects($this->once())
            ->method('andWhere')
            ->with($this->equalTo('e.'. $optionList['dependentProperty'] .' = :dependentProperty'));

        $this->criteriaMock
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->equalTo(':dependentProperty'),
                $this->equalTo($value)
            );

        $this->assertEquals(
            $this->criteriaMock,
            $this->service->buildCriteria(
                $this->entityManagerMock,
                $value,
                $optionList
            )
        );
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array(
                'value', array('class' => 'IC\Bundle\Base\ComponentBundle\Entity\Program', 'dependentProperty' => 'type')
            )
        );
    }
}
