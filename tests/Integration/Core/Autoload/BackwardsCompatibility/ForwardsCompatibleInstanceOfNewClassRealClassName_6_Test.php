<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardsCompatibleInstanceOfNewClassRealClassName_6_Test extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Test the backwards compatibility of class instances created with oxNew and the alias class name
     *
     * @runInSeparateProcess
     */
    public function testForwardsCompatibleInstanceOfNewClassRealClassName()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $realClassName = \OxidEsales\EshopCommunity\Application\Model\Article::class;
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $backwardsCompatibleClassAlias = 'oxArticle';
        $message = 'Backwards compatible class name - CamelCase string';
        
        $object = new $realClassName();

        $this->assertInstanceOf($backwardsCompatibleClassAlias, $object, $message);

        $this->assertInstanceOf($realClassName, $object, $message);

        $this->assertInstanceOf($virtualClassName, $object, $message);
    }
}
