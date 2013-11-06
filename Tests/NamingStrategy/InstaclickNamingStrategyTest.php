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
            'className' => 'IC\Bundle\Personal\IntimacyBundle\Entity\Intimacy',
            'expected'  => 'PersonalIntimacyEntity'
        );

        $data[] = array(
            'className' => 'IC\Bundle\Csr\PitBundle\Entity\Queue',
            'expected'  => 'CsrPitQueue'
        );

        $data[] = array(
            'className' => 'IC\Bundle\Affiliate\ProfileBundle\Entity\Blacklist',
            'expected'  => 'AffiliateProfileBlacklist'
        );

        $data[] = array(
            'className' => 'IC\Bundle\Seo\TreeBundle\Entity\SexualPreference',
            'expected'  => 'SeoTreeSexualPreference'
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
            'sourceEntity' => 'IC\Bundle\Seo\TreeBundle\Entity\Profile',
            'targetEntity' => 'IC\Bundle\Seo\TreeBundle\Entity\SexualPreference',
            'expected'     => 'SeoTreeProfile_SeoTreeSexualPreference'
        );

        $data[] = array(
            'sourceEntity' => 'IC\Bundle\Personal\ProfileBundle\Entity\Profile',
            'targetEntity' => 'IC\Bundle\Core\SecurityBundle\Entity\BannedEmail',
            'expected'     => 'PersonalProfileEntity_CoreSecurityBannedEmail'
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
            'entityName'          => 'IC\Bundle\Seo\TreeBundle\Entity\Profile',
            'referenceColumnName' => '',
            'expected'            => 'seotreeprofile_id'
        );

        $data[] = array(
            'entityName'          => 'IC\Bundle\Personal\ProfileBundle\Entity\Profile',
            'referenceColumnName' => '',
            'expected'            => 'personalprofileentity_id'
        );

        $data[] = array(
            'entityName'          => 'IC\Bundle\Affiliate\ProfileBundle\Entity\Blacklist',
            'referenceColumnName' => 'mockcolumnname',
            'expected'            => 'affiliateprofileblacklist_mockcolumnname'
        );

        return $data;
    }
}
