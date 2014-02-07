<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Form\Subscriber;

use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Base\ComponentBundle\Form\Subscriber\DependentEntityFormSubscriber;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;

/**
 * Dependent Entity Form Subscriber Test
 *
 * @group ICBaseComponent
 * @group Subscriber
 * @group Unit
 *
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class DependentEntityFormSubscriberTest extends TestCase
{
    /**
     * Test the expected events are subscribed to.
     */
    public function testSubscribedEvents()
    {
        $subscriber = new DependentEntityFormSubscriber(
            $this->createMock('Symfony\Component\Form\FormBuilder'),
            $this->createMock('IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterServiceInterface')
        );

        $subscribedEventList = $subscriber->getSubscribedEvents();

        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $subscribedEventList);
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $subscribedEventList);
    }

    /**
     * Test on field rebind without a valid dataset does not rebind the field.
     */
    public function testOnFieldRebindWithNoDataDoesNotRebindField()
    {
        $subscriber = new DependentEntityFormSubscriber(
            $this->createMock('Symfony\Component\Form\FormBuilder'),
            $this->createMock('IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterServiceInterface')
        );

        $event = new FormEvent(
            $this->createUnusedFormMock(),
            array()
        );

        $subscriber->onFieldRebind($event);
    }

    /**
     * Test on field rebind with a valid dataset does rebinds the field.
     *
     * @param string                      $fieldName
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \Doctrine\ORM\QueryBuilder  $queryBuilder
     * @param array                       $inputFieldOptionList
     * @param array                       $expectedFieldOptionList
     * @param array                       $data
     * @param string                      $expectedDependentFieldValue
     *
     * @dataProvider fieldRebindDataProvider
     */
    public function testOnFieldRebindWithValidDataRebindsField(
        $fieldName,
        $entityManager,
        $queryBuilder,
        $inputFieldOptionList,
        $expectedFieldOptionList,
        $data,
        $expectedDependentFieldValue
    ) {
        $formBuilder   = $this->createFormBuilderMock($fieldName, $inputFieldOptionList);
        $filterService = $this->createFilterServiceMock($entityManager, $queryBuilder, $expectedDependentFieldValue, $inputFieldOptionList);

        $subscriber = new DependentEntityFormSubscriber($formBuilder, $filterService);

        $event = new FormEvent(
            $this->createFormMock($fieldName, $expectedFieldOptionList),
            $data
        );

        $subscriber->onFieldRebind($event);
    }

    /**
     * Data provider for a valid rebind scenario
     *
     * @return array
     */
    public function fieldRebindDataProvider()
    {
        $entityManager = $this->createMock('Doctrine\ORM\EntityManager');
        $queryBuilder  = $this->createMock('Doctrine\ORM\QueryBuilder');

        return array(
            array(
                'fieldName',
                $entityManager,
                $queryBuilder,
                array(
                    'em'             => $entityManager,
                    'dependentField' => 'dependentFieldName',
                    'class'          => 'IC/Base/Foobar/Entity',
                    'property'       => 'fieldProperty',
                    'empty_value'    => '',
                    'multiple'       => false,
                    'expanded'       => false,
                ),
                array(
                    'class'         => 'IC/Base/Foobar/Entity',
                    'property'      => 'fieldProperty',
                    'query_builder' => $queryBuilder,
                    'empty_value'   => '',
                    'multiple'      => false,
                    'expanded'      => false,
                ),
                array(
                    'dependentFieldName' => 'dependentFieldValue',
                ),
                'dependentFieldValue',
            ),
        );
    }

    /**
     * Test on field rebind with a valid dataset does rebinds the field.
     *
     * @param string $fieldName
     * @param array  $inputFieldOptionList
     * @param array  $data
     *
     * @dataProvider nullDependentFieldRebindDataProvider
     */
    public function testOnFieldRebindWithNullDependentFieldDataDoesNotRebindsField(
        $fieldName,
        $inputFieldOptionList,
        $data
    ) {
        $subscriber = new DependentEntityFormSubscriber(
            $this->createFormBuilderMock($fieldName, $inputFieldOptionList),
            $this->createMock('IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterServiceInterface')
        );

        $event = new FormEvent(
            $this->createUnusedFormMock(),
            $data
        );

        $subscriber->onFieldRebind($event);
    }

    /**
     * Data provider for a null dependent field no rebind scenario
     *
     * @return array
     */
    public function nullDependentFieldRebindDataProvider()
    {
        $entityManager = $this->createMock('Doctrine\ORM\EntityManager');
        $queryBuilder  = $this->createMock('Doctrine\ORM\QueryBuilder');

        return array(
            array(
                'fieldName',
                array(
                    'em'             => $entityManager,
                    'dependentField' => 'dependentFieldName',
                    'class'          => 'IC/Base/Foobar/Entity',
                    'property'       => 'fieldProperty',
                    'empty_value'    => '',
                ),
                array(
                    'dependentFieldName' => null,
                ),
            ),
            array(
                'fieldName',
                array(
                    'em'             => $entityManager,
                    'dependentField' => 'dependentFieldName',
                    'class'          => 'IC/Base/Foobar/Entity',
                    'property'       => 'fieldProperty',
                    'empty_value'    => '',
                ),
                array(
                    'dependentFieldName' => '',
                ),
            ),
        );
    }

    /**
     * Create a Filter Service Mock
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \Doctrine\ORM\QueryBuilder  $queryBuilder
     * @param mixed                       $dependentValue
     * @param array                       $optionList
     *
     * @return \IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterServiceInterface
     */
    private function createFilterServiceMock(EntityManager $entityManager, QueryBuilder $queryBuilder, $dependentValue, $optionList)
    {
        $filterService = $this->createMock('IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterServiceInterface');

        $filterService
            ->expects($this->once())
            ->method('buildCriteria')
            ->with(
                $this->equalTo($entityManager),
                $this->equalTo($dependentValue),
                $this->equalTo($optionList)
            )
            ->will($this->returnValue($queryBuilder));

        return $filterService;
    }

    /**
     * Create a Form Mock
     *
     * @param string $name
     * @param array  $optionList
     *
     * @return \IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterServiceInterface
     */
    private function createFormMock($name, $optionList)
    {
        $form = $this->createMock('Symfony\Component\Form\Form');

        $form
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo($name),
                $this->equalTo('entity'),
                $this->equalTo($optionList)
            );

        return $form;
    }

    /**
     * Create a Form Mock that does not expect to be used
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createUnusedFormMock()
    {
        $form = $this->createMock('Symfony\Component\Form\Form');

        $form
            ->expects($this->never())
            ->method('add');

        return $form;
    }

    /**
     * Create a Form Builder Mock
     *
     * @param string $name
     * @param array  $optionList
     *
     * @return \Symfony\Component\Form\FormBuilder
     */
    private function createFormBuilderMock($name, $optionList)
    {
        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilder');

        $formBuilder
            ->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($optionList));

        $formBuilder
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $formBuilder;
    }
}
