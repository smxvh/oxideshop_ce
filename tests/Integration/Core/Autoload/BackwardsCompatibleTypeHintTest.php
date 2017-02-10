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

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload;

use OxidEsales\TestingLibrary\UnitTestCase;

class BackwardsCompatibleTypeHintTest extends UnitTestCase
{

    public function setUp()
    {
        parent::setUp();
        class_alias(\OxidEsales\EshopCommunity\Application\Model\Article::class, 'oxArticle');
        class_alias(\OxidEsales\EshopCommunity\Application\Model\Article::class, \OxidEsales\Eshop\Application\Model\Article);
    }

    /**
     * Test the backwards compatibilyty with camel cased type hints
     *
     * @dataProvider dataProviderObjects
     *
     * @param object $object
     */
    public function testBackwardsCompatibleTypeHintCamelCase($object)
    {
        /**
         * @param oxArticle $object
         */
        $functionWithTypeHint = function (oxArticle $object) {
            /** If the function was called successfully, the test would have passed */
            $this->assertTrue(1);
        };
        /** The function call would produce a catchable fatal error, if the type hint is not correct */
        $functionWithTypeHint($object);
    }

    /**
     * Test the backwards compatibilyty with camel cased type hints
     *
     * @dataProvider dataProviderObjects
     *
     * @param object $object
     */
    public function testBackwardsCompatibleTypeHintLowerCase($object) {
        /**
         * @param oxarticle $object
         */
        $functionWithTypeHint = function (oxarticle $object) {
            /** If the function was called successfully, the test would have passed */
            $this->assertTrue(1);
        };
        /** The function call would produce a catchable fatal error, if the type hint is not correct */
        $functionWithTypeHint($object);
    }

    /**
     * Test the backwards compatibilyty with camel cased type hints
     *
     * @dataProvider dataProviderObjects
     *
     * @param object $object
     */
    public function testForwardCompatibleTypeHintWithCommunityNamespace($object)
    {
        /**
         * @param \OxidEsales\EshopCommunity\Application\Model\Article $object
         */
        $functionWithTypeHint = function (\OxidEsales\EshopCommunity\Application\Model\Article $object) {
            /** If the function was called successfully, the test would have passed */
            $this->assertTrue(1);
        };
        /** The function call would produce a catchable fatal error, if the type hint is not correct */
        $functionWithTypeHint($object);
    }

    /**
     * Test the backwards compatibilyty with camel cased type hints
     *
     * @dataProvider dataProviderObjectsOxNew
     *
     * @param object $object
     */
    public function testForwardCompatibleTypeHintWithVirtualNamespace($object) {
        /**
         * @param \OxidEsales\Eshop\Application\Model\Article $object
         */
        $functionWithTypeHint = function (\OxidEsales\Eshop\Application\Model\Article $object) {
            /** If the function was called successfully, the test would have passed */
            $this->assertTrue(1);
        };
        /** The function call would produce a catchable fatal error, if the type hint is not correct */
        $functionWithTypeHint($object);
    }

    public function dataProviderObjects() {
        return array_merge($this->dataProviderObjectsOxNew(), $this->dataProviderObjectsNew());
    }

    public function dataProviderObjectsOxNew() {
        return [
          ['object' => oxNew('oxArticle')],
          ['object' => oxNew(\oxArticle::class)],
          ['object' => oxNew('oxarticle')],
          ['object' => new \oxArticle()],
        ];
    }

    public function dataProviderObjectsNew() {
        return [
          ['object' => new \oxArticle()],
        ];
    }
}

