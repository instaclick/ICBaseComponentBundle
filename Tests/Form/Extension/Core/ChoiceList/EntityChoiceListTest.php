<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Form\Extension\Core\ChoiceList;

use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Base\ComponentBundle\Form\Extension\Core\ChoiceList\EntityChoiceList;

/**
 * Test EntityChoiceList
 *
 * @group Unit
 * @group Form
 * @group FormExtension
 *
 * @author Clive Zagno <clivez@nationalfibre.net>
 */
class EntityChoiceListTest extends TestCase
{
    /**
     * Test that a list of BaseComponent/Entity is able to be filtered by id
     */
    public function testPreffedViewsWithEntity()
    {
        $entityHelper = $this->getHelper('Unit\Entity');

        $entities[] = $this->createEntity('US', 'United States');
        $entities[] = $this->createEntity('CA', 'Canada');
        $entities[] = $this->createEntity('ZA', 'South Africa');

        $choiceList = new EntityChoiceList($entities, 'name', array('CA'), null, 'id');

        $preferredViews = $choiceList->getPreferredViews();
        $preferredView  = array_shift($preferredViews);

        $this->assertEquals($preferredView->label, "Canada");
        $this->assertEquals($preferredView->value, "CA");
    }

    /**
     * Test that we can only allow BaseComponent/Entity
     *
     * @expectedException \InvalidArgumentException
     */
    public function testWithNonEntity()
    {
        $entity       =  new \stdClass();
        $entity->name = 'name';

        $choiceList = new EntityChoiceList(array($entity), 'name');
    }

    private function createEntity($id, $name)
    {
        $entityHelper = $this->getHelper('Unit\Entity');
        $entity       = $entityHelper->createMock('IC\Bundle\Base\ComponentBundle\Entity\Entity', $id);
        $entity->name = $name;

        return $entity;
    }
}
