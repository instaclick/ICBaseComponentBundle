<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\Validator\Constraints;

use IC\Bundle\Base\ComponentBundle\Tests\MockObject\Form\Model\EntityNotIdenticalFormModel;
use IC\Bundle\Base\ComponentBundle\Validator\Constraints\EntityNotIdentical;
use IC\Bundle\Base\ComponentBundle\Validator\Constraints\EntityNotIdenticalValidator;
use IC\Bundle\Base\TestBundle\Test\Validator\ValidatorTestCase;

/**
 * Unit Test for EntityNotIdenticalValidator
 *
 * @group Validator
 * @group Unit
 * @group ICBaseComponentBundle
 *
 * @author Oleksandr Kovalov <oleksandrk@nationalfibre.net>
 * @author Oleksii Strutsynskyi <oleksiis@nationalfibre.net>
 */
class EntityNotIdenticalValidatorTest extends ValidatorTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->validator    = new EntityNotIdenticalValidator();
        $this->entityHelper = $this->getHelper('Unit\Entity');
        $this->constraint   = $this->getConstraint();
    }

    /**
     * Test entities are not identical.
     *
     * @param integer $sourceId
     * @param integer $targetId
     *
     * @dataProvider validDataProvider
     */
    public function testValid($sourceId, $targetId)
    {
        $formModel = $this->getFormModel($sourceId, $targetId);

        $this->assertValid($this->validator, $this->constraint, $formModel);
    }

    /**
     * Valid data provider.
     *
     * @return array
     */
    public function validDataProvider()
    {
        return array(
            array(null, 1),
            array(1, null),
            array(1, 2)
        );
    }

    /**
     * Test when entities are identical (invalid validation).
     *
     * @param integer $sourceId
     * @param integer $targetId
     *
     * @dataProvider invalidDataProvider
     */
    public function testInvalid($sourceId, $targetId)
    {
        $formModel = $this->getFormModel($sourceId, $targetId);

        $this->assertInvalidAtSubPath($this->validator, $this->constraint, $formModel, "target", $this->constraint->message);
    }

    /**
     * Invalid data provider.
     *
     * @return array
     */
    public function invalidDataProvider()
    {
        return array(
            array(1, 1),
            array(2, 2)
        );
    }

    /**
     * Test without source and target entities in the formModel.
     */
    public function testWithoutSourceAndTarget()
    {
        $formModel = new EntityNotIdenticalFormModel();

        $this->assertValid($this->validator, $this->constraint, $formModel);
    }

    /**
     * Prepare form model
     *
     * @param int $sourceId
     * @param int $targetId
     *
     * @return \IC\Bundle\Base\ComponentBundle\Tests\MockObject\Form\Model\EntityNotIdenticalFormModel
     */
    private function getFormModel($sourceId, $targetId)
    {
        $formModel = new EntityNotIdenticalFormModel();

        $formModel->setSource($this->createEntity($sourceId));
        $formModel->setTarget($this->createEntity($targetId));

        return $formModel;
    }

    /**
     * Create the entity.
     *
     * @param mixed $entityId integer | null
     *
     * @return mixed IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity | null
     */
    private function createEntity($entityId)
    {
        if ( ! $entityId) {
            return null;
        }

        return $this->entityHelper->createMock('IC\Bundle\Base\ComponentBundle\Tests\MockObject\Entity\Entity', $entityId);
    }

    /**
     * Prepare validator constraint
     *
     * @return \IC\Bundle\Base\ComponentBundle\Validator\Constraints\EntityNotIdentical
     */
    private function getConstraint()
    {
        $constraint = new EntityNotIdentical();

        $constraint->sourceEntity = 'source';
        $constraint->targetEntity = 'target';

        return $constraint;
    }
}
