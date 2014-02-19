<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\NamingStrategy;

use IC\Bundle\Base\ComponentBundle\NamingStrategy\InstaclickNamingStrategy;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Test for Instaclick Naming Strategy class
 *
 * @group Unit
 *
 * @author David Maignan <davidm@nationalfibre.net>
 */
class InstaclickNamingStrategyTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\NamingStrategy\InstaclickNamingStrategy
     */
    private $strategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->strategy = new InstaclickNamingStrategy();
    }

    /**
     * Test class to table name
     *
     * @param string $className Class name
     * @param string $expected  Table name
     *
     * @dataProvider dataProviderClassToTableName
     */
    public function testClassToTableName($className, $expected)
    {
        $result = $this->strategy->classToTableName($className);

        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for classToTableName method
     *
     * @return array
     */
    public function dataProviderClassToTableName()
    {
        $data = array();

        $data[] = array(
            'className' => 'IC\Bundle\Base\ComponentBundle\Entity\Component',
            'expected'  => 'BaseComponentEntity'
        );

        $data[] = array(
            'className' => 'IC\Bundle\Base\ComponentBundle\Entity\Hello',
            'expected'  => 'BaseComponentHello'
        );

        $data[] = array(
            'className' => 'Doctrine\ORM\Mapping\NamingStrategy',
            'expected'  => 'Doctrine\ORM\Mapping\NamingStrategy'
        );

        return $data;
    }

    /**
     * Test propertyToColumnName method
     */
    public function testPropertyToColumnName()
    {
        $propertyName = 'mockPropertyName';

        $result = $this->strategy->propertyToColumnName($propertyName);

        $this->assertEquals($propertyName, $result);
    }

    /**
     * Test referenceToColumnName method
     */
    public function testReferenceToColumnName()
    {
        $this->assertEquals('id', $this->strategy->referenceColumnName());
    }

    /**
     * Test joinColumnName method
     */
    public function testJoinColumnName()
    {
        $propertyName = 'property_name';
        $result       = $this->strategy->joinColumnName($propertyName);

        $this->assertEquals('property_name_id', $result);
    }

    /**
     * Test Join table name method
     *
     * @param string $sourceEntity Source entity
     * @param string $targetEntity Target entity
     * @param string $expected     Join Table Name
     *
     * @dataProvider dataProviderJoinTableName
     */
    public function testJoinTableName($sourceEntity, $targetEntity, $expected)
    {
        $result = $this->strategy->joinTableName($sourceEntity, $targetEntity);

        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for JoinColumnName method
     *
     * @return array
     */
    public function dataProviderJoinTableName()
    {
        $data = array();

        $data[] = array(
            'sourceEntity' => 'IC\Bundle\Base\ComponentBundle\Entity\Hello',
            'targetEntity' => 'IC\Bundle\Base\ComponentBundle\Entity\World',
            'expected'     => 'BaseComponentHello_BaseComponentWorld'
        );

        $data[] = array(
            'sourceEntity' => 'Doctrine\ORM\Mapping\NamingStrategy',
            'targetEntity' => 'Elastica\Bulk\Response',
            'expected'     => 'Doctrine\ORM\Mapping\NamingStrategy_Elastica\Bulk\Response'
        );

        return $data;
    }

    /**
     * Test joinKeyColumnName method
     *
     * @param string      $entityName           Full qualify name of the entity
     * @param string|null $referencedColumnName Column name
     * @param string      $expected             Result to check against
     *
     * @dataProvider dataProviderJoinKeyColumnName
     */
    public function testJoinKeyColumnName($entityName, $referencedColumnName, $expected)
    {
        $result = $this->strategy->joinKeyColumnName($entityName, $referencedColumnName);

        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for joinKeyColumnName method
     *
     * @return array
     */
    public function dataProviderJoinKeyColumnName()
    {
        $data = array();

        $data[] = array(
            'entityName'          => 'IC\Bundle\Base\ComponentBundle\Entity\Hello',
            'referenceColumnName' => '',
            'expected'            => 'basecomponenthello_id'
        );

        $data[] = array(
            'entityName'          => 'IC\Bundle\Base\ComponentBundle\Entity\Component',
            'referenceColumnName' => '',
            'expected'            => 'basecomponententity_id'
        );

        $data[] = array(
            'entityName'          => 'IC\Bundle\Base\ComponentBundle\Entity\Hello',
            'referenceColumnName' => 'mockcolumnname',
            'expected'            => 'basecomponenthello_mockcolumnname'
        );

        return $data;
    }

    /**
     * Returns a column name for an embedded property.
     *
     * @param string $propertyName
     * @param string $embeddedColumnName
     * @param mixed  $className
     * @param mixed  $embeddedClassName
     * @param string $expected
     *
     * @return string
     *
     * @dataProvider dataProviderEmbeddedFieldToColumnName
     */
    public function testEmbeddedFieldToColumnName($propertyName, $embeddedColumnName, $className, $embeddedClassName, $expected)
    {
        $actual = $this->strategy->embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className, $embeddedClassName);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for the testEmbeddedFieldToColumnName method
     *
     * @return array
     */
    public function dataProviderEmbeddedFieldToColumnName()
    {
        $data = array();

        $data[] = array(
            'propertyName'       => 'a',
            'embeddedColumnName' => 'b',
            'className'          => null,
            'embeddedClassName'  => null,
            'expected'           => 'a_b'
        );

        $data[] = array(
            'propertyName'       => 'c',
            'embeddedColumnName' => 'd',
            'className'          => 'e',
            'embeddedClassName'  => 'f',
            'expected'           => 'c_d'
        );

        return $data;
    }
}
