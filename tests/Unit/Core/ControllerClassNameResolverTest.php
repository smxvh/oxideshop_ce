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
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class ControllerClassNameResolverTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Core
 */
class ControllerClassNameResolverTest extends UnitTestCase
{

    /**
     * Test getter for ShopControllerProvider
     */
    public function testGetShopControllerProvider()
    {
        $resolver = oxNew('OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver');
        $this->assertTrue(is_a($resolver->getShopControllerProvider(), 'OxidEsales\EshopCommunity\Core\Routing\ShopControllerProvider'));
    }

    /**
     * Test getter for ModuleControllerProvider
     */
    public function testGetModuleControllerProvider()
    {
        $resolver = oxNew('OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver');
        $this->assertTrue(is_a($resolver->getModuleControllerProvider(), 'OxidEsales\EshopCommunity\Core\Routing\ModuleControllerProvider'));
    }

    /**
     * Test mapping class name to id, result found in shop controller map.
     */
    public function testGetClassNameByIdFromShop()
    {
        $resolver = $this->getMock('OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver', array('getModuleControllerProvider', 'getShopControllerProvider'));
        $resolver->expects($this->once())->method('getShopControllerProvider')->will($this->returnValue($this->getShopControllerProviderMock()));
        $resolver->expects($this->never())->method('getModuleControllerProvider');

        $this->assertEquals('OxidEsales\EshopCommunity\Application\SomeOtherController', $resolver->getClassNameById('bbb'));
    }

    /**
     * Test mapping class name to id, result found in module controller map.
     */
    public function testGetClassNameByIdFromModule()
    {
        $resolver = $this->getMock('OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver', array('getModuleControllerProvider', 'getShopControllerProvider'));
        $resolver->expects($this->once())->method('getShopControllerProvider')->will($this->returnValue($this->getShopControllerProviderMock()));
        $resolver->expects($this->once())->method('getModuleControllerProvider')->will($this->returnValue($this->getModuleControllerProviderMock()));

        $this->assertEquals('Vendor2\OtherTestModule\SomeDifferentController', $resolver->getClassNameById('eee'));
    }

    /**
     * Test mapping class name to id, no result found in either map.
     */
    public function testGetClassNameByIdNoMatch()
    {
        $resolver = $this->getMock('OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver', array('getModuleControllerProvider', 'getShopControllerProvider'));
        $resolver->expects($this->once())->method('getShopControllerProvider')->will($this->returnValue($this->getShopControllerProviderMock()));
        $resolver->expects($this->once())->method('getModuleControllerProvider')->will($this->returnValue($this->getModuleControllerProviderMock()));

        $this->assertNull($resolver->getClassNameById('zzz'));
    }

    /**
     * Verify that finding a match is not type sensitive.
     */
    public function testGetClassNameByIdNotTypeSensitive()
    {
        $resolver = $this->getMock('OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver', array('getModuleControllerProvider', 'getShopControllerProvider'));
        $resolver->expects($this->once())->method('getShopControllerProvider')->will($this->returnValue($this->getShopControllerProviderMock()));
        $resolver->expects($this->never())->method('getModuleControllerProvider');

        $this->assertEquals('OxidEsales\EshopCommunity\Application\SomeDifferentController', $resolver->getClassNameById('ccc'));
    }

    /**
     * Test mapping id to class name, result found in shop controller map.
     */
    public function testGetIdByClassNameFromShop()
    {
        $resolver = $this->getMock('OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver', array('getModuleControllerProvider', 'getShopControllerProvider'));
        $resolver->expects($this->once())->method('getShopControllerProvider')->will($this->returnValue($this->getShopControllerProviderMock()));
        $resolver->expects($this->never())->method('getModuleControllerProvider');

        $this->assertEquals('bbb', $resolver->getIdByClassName('OxidEsales\EshopCommunity\Application\SomeOtherController'));
    }

    /**
     * Test mapping id to class name, result found in module controller map.
     */
    public function testGetIdByClassNameFromModule()
    {
        $resolver = $this->getMock('OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver', array('getModuleControllerProvider', 'getShopControllerProvider'));
        $resolver->expects($this->once())->method('getShopControllerProvider')->will($this->returnValue($this->getShopControllerProviderMock()));
        $resolver->expects($this->once())->method('getModuleControllerProvider')->will($this->returnValue($this->getModuleControllerProviderMock()));

        $this->assertEquals('eee', $resolver->getIdByClassName('Vendor2\OtherTestModule\SomeDifferentController'));
    }

    /**
     * Test mapping id to class name, no result found in either map.
     */
    public function testGetIdByClassNameNoMatch()
    {
        $resolver = $this->getMock('OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver', array('getModuleControllerProvider', 'getShopControllerProvider'));
        $resolver->expects($this->once())->method('getShopControllerProvider')->will($this->returnValue($this->getShopControllerProviderMock()));
        $resolver->expects($this->once())->method('getModuleControllerProvider')->will($this->returnValue($this->getModuleControllerProviderMock()));

        $this->assertNull($resolver->getIdByClassName('novendor\noclass'));
    }

    /**
     * Verify that finding a match is not type sensitive.
     */
    public function testGetIdByClassNameNotTypeSensitive()
    {
        $resolver = $this->getMock('OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver', array('getModuleControllerProvider', 'getShopControllerProvider'));
        $resolver->expects($this->once())->method('getShopControllerProvider')->will($this->returnValue($this->getShopControllerProviderMock()));
        $resolver->expects($this->once())->method('getModuleControllerProvider')->will($this->returnValue($this->getModuleControllerProviderMock()));

        $this->assertEquals('eee', $resolver->getIdByClassName(strtolower('Vendor2\OtherTestModule\SomeDifferentController')));
    }

    /**
     * Test helper
     *
     * @return OxidEsales\EshopCommunity\Core\ShopControllerProvider mock
     */
    private function getShopControllerProviderMock()
    {
        $map = array('aAa' => 'OxidEsales\EshopCommunity\Application\SomeController',
                     'bbb' => 'OxidEsales\EshopCommunity\Application\SomeOtherController',
                     'CCC' => 'OxidEsales\EshopCommunity\Application\SomeDifferentController');

        $mock = $this->getMock('OxidEsales\EshopCommunity\Core\Routing\ShopControllerProvider', ['getControllerMap'], [], '', false);
        $mock->expects($this->any())->method('getControllerMap')->will($this->returnValue($map));

        return $mock;
    }

    /**
     * Test helper
     *
     * @return OxidEsales\EshopCommunity\Core\ModuleControllerProvider mock
     */
    private function getModuleControllerProviderMock()
    {
        $map = array('cCc' => 'Vendor1\Testmodule\SomeController',
                     'DDD' => 'Vendor1\OtherTestModule\SomeOtherController',
                     'eee' => 'Vendor2\OtherTestModule\SomeDifferentController');

        $mock = $this->getMock('OxidEsales\EshopCommunity\Core\Routing\ModuleControllerProvider', ['getControllerMap'], [], '', false);
        $mock->expects($this->any())->method('getControllerMap')->will($this->returnValue($map));

        return $mock;
    }
}

