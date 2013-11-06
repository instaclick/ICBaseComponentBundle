<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Tests\DependencyInjection;

use IC\Bundle\Base\TestBundle\Test\DependencyInjection\ExtensionTestCase;

use IC\Bundle\Base\ComponentBundle\DependencyInjection\ICBaseComponentExtension;

/**
 * Test for ICBaseComponentExtension
 *
 * @group ICBaseComponentBundle
 * @group Unit
 * @group DependencyInjection
 *
 * @author Yuan Xie <shayx@nationalfibre.net>
 */
class ICBaseComponentExtensionTest extends ExtensionTestCase
{
    /**
     * Test configuration
     */
    public function testConfiguration()
    {
        $loader = new ICBaseComponentExtension();

        $this->load($loader, array());
    }
}
