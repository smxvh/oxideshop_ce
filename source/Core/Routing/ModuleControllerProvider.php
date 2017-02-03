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

namespace OxidEsales\EshopCommunity\Core\Routing;

use OxidEsales\EshopCommunity\Core\Contract\ControllerProviderInterface;

/**
 * This class determines the controllers which should be allowed to be called directly via
 * HTTP GET/POST Parameters, inside form actions or with oxid_include_widget.
 * Those controllers are specified e.g. inside a form action with a controller key which is mapped to its class.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleControllerProvider implements ControllerProviderInterface
{

    private $controllerMap = [];

    /**
     * All available controller maps will be merged together like this: CE <- PE <- EE
     *
     * @return array Edition specific mapping of controller keys to classes
     */
    public function getControllerMap()
    {
        return $this->controllerMap;
    }
}
