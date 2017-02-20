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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;

/**
 * Class responsible for returning class map by edition.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ClassMapProvider
{
    /**
     * Sets edition selector object.
     *
     * @param EditionSelector $editionSelector
     */
    public function __construct($editionSelector)
    {
        $this->editionSelector = $editionSelector;
    }

    /**
     * Return a map of concrete classes to virtual namespaced classes depending on the shop edition.
     * All available class maps will be merged together like this: CE <- PE <- EE
     *
     * @return array Edition specific class map
     */
    public function getOverridableVirtualNamespaceClassMap()
    {
        $editionSelector = $this->getEditionSelector();
        switch ($editionSelector->getEdition()) {
            case EditionSelector::ENTERPRISE:
                $classMapCommunity = new \OxidEsales\EshopCommunity\Core\VirtualNameSpaceClassMap();
                $classMapProfessional = new \OxidEsales\EshopProfessional\Core\VirtualNameSpaceClassMap();
                $classMapEnterprise = new \OxidEsales\EshopEnterprise\Core\VirtualNameSpaceClassMap();
                $virtualNameSpaceClassMap = array_merge(
                    $classMapCommunity->getOverridableMap(),
                    $classMapProfessional->getOverridableMap(),
                    $classMapEnterprise->getOverridableMap()
                );
                break;
            case EditionSelector::PROFESSIONAL:
                $classMapCommunity = new \OxidEsales\EshopCommunity\Core\VirtualNameSpaceClassMap();
                $classMapProfessional = new \OxidEsales\EshopProfessional\Core\VirtualNameSpaceClassMap();
                $virtualNameSpaceClassMap = array_merge(
                    $classMapCommunity->getOverridableMap(),
                    $classMapProfessional->getOverridableMap()
                );
                break;
            default:
            case EditionSelector::COMMUNITY:
                $classMapCommunity = new \OxidEsales\EshopCommunity\Core\VirtualNameSpaceClassMap();
                $virtualNameSpaceClassMap = $classMapCommunity->getOverridableMap();
                break;
        }

        return $virtualNameSpaceClassMap;
    }

    /**
     * Getter for edition selector.
     *
     * @return EditionSelector
     */
    protected function getEditionSelector()
    {
        return $this->editionSelector;
    }
}
