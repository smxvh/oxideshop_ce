<?php
namespace OxidEsales\Development\Testmodules\TestModuleRoutingESDEV4240;

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

/**
 * Metadata version
 */
use OxidEsales\Development\Testmodules\testmoduleRoutingESDEV4240\Application\Controller\ExampleController1;

$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'metadataversion' => '2.0',
    'id'              => 'testmoduleroutingesdev4240',
    'title'           => array(
        'de' => 'Test module Routing ESDEV-4240',
        'en' => 'Test module Routing ESDEV-4240',
    ),
    'description'     => array(
        'de' => 'Test module for implementation of story ESDEV-4240',
        'en' => 'Test module for implementation of story ESDEV-4240',
    ),
    'thumbnail'       => 'out/pictures/logo.png',
    'version'         => '1.0.0',
    'author'          => 'OXID eSales',
    'url'             => 'https://www.oxid-esales.com/',
    'email'           => 'info@oxid-esales.com',
    'controllers'     => [
        'testmodule-routing-ESDEV-4240-controller-1' => \OxidEsales\Eshop\Application\Controller\Admin\LanguageController::class,
    ]
);
