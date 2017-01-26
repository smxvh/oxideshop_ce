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
namespace Unit\Core\Routing\Module;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\Routing\Module\ClassProviderStorage;

/**
 * Test the module controller provider cache.
 *
 * @package Unit\Core\Routing\Module
 */
class ControllerProviderCacheTest extends UnitTestCase
{
    /**
     * Standard setup method, called before every method.
     *
     * Calls parent method first.
     */
    protected function setUp()
    {
        parent::setUp();

        $controllerProviderCache = oxNew(ClassProviderStorage::class);
        $controllerProviderCache->reset();
    }

    /**
     * Test, that the creation works properly.
     *
     * @return ClassProviderStorage A fresh controller provider cache.
     */
    public function testCreation()
    {
        $controllerProviderCache = oxNew(ClassProviderStorage::class);

        $this->assertTrue(is_a($controllerProviderCache, ClassProviderStorage::class));

        return $controllerProviderCache;
    }

    /**
     * Test, that the cache leads to null, if we don't set anything before.
     */
    public function testGetWithoutSetValueBefore()
    {
        $cache = $this->testCreation();

        $result = $cache->get();

        $this->assertNull($result);

        return $cache;
    }

    /**
     * Test, that the cache leads to the before set value.
     */
    public function testGetWithSetValueBefore()
    {
        $value = ['ModuleA' => []];

        $cache = $this->testCreation();
        $cache->set($value);

        $this->assertEquals($cache->get(), $value);

        return $cache;
    }

    /**
     * Test, that the method isEmpty gives back true, if the value isn't set.
     */
    public function testIsEmptyNotSetBefore()
    {
        $cache = $this->testCreation();

        $this->assertTrue($cache->isEmpty());
    }

    /**
     * Test, that the method isEmpty gives back false, if the value is set.
     */
    public function testIsEmptySetBefore()
    {
        $cache = $this->testGetWithSetValueBefore();

        $this->assertFalse($cache->isEmpty());
    }

    /**
     * Test, that the method reset removes the value of the cache.
     */
    public function testResetSetValueBefore()
    {
        $cache = $this->testGetWithSetValueBefore();

        $this->assertFalse($cache->isEmpty());
        $cache->reset();
        $this->assertTrue($cache->isEmpty());
    }
}
