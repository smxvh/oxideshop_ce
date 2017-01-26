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
namespace OxidEsales\EshopCommunity\Core\Routing\Module;

use OxidEsales\EshopCommunity\Core\Contract\ClassProviderStorageInterface;
use OxidEsales\Eshop\Core\Registry;

/**
 * Handler class for the caching of the metadata controller field of the modules.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class ClassProviderStorage implements ClassProviderStorageInterface
{
    /**
     * @var string The key under which the value will be cached.
     */
    const CACHE_KEY = 'aModuleControllers';

    /**
     * Get the stored controller value from the oxconfig.
     *
     * @return array The controllers field of the modules metadata.
     */
    public function get()
    {
        return $this->getConfig()->getShopConfVar(self::CACHE_KEY);
    }

    /**
     * Get the stored controller value from the oxconfig.
     *
     * @param array $value The controllers field of the modules metadata.
     */
    public function set($value)
    {
        $this->getConfig()->saveShopConfVar('aarr', self::CACHE_KEY, $value);
    }

    /**
     * Add the controllers for the module, given by its ID, to the cache.
     *
     * @param string $moduleId    The ID of the module controllers to add.
     * @param array  $controllers The controllers to add to the cache.
     */
    public function add($moduleId, $controllers)
    {
        // @todo Validation Assure that keys and values are unique allover the modules and the shops controllerMap !!
        $controllerMap = (array) $this->getConfig()->getConfigParam('aModuleControllers');
        $controllerMap[$moduleId] = $controllers;

        $this->set($controllerMap);
    }

    /**
     * Delete the controllers for the module, given by its ID, from the cache.
     *
     * @param string $moduleId The ID of the module, for which we want to delete the controllers from the cache.
     */
    public function remove($moduleId)
    {
        $controllerMap = (array) $this->getConfig()->getConfigParam('aModuleControllers');
        unset($controllerMap[$moduleId]);

        $this->set($controllerMap);
    }

    /**
     * Unset the cached value.
     */
    public function reset()
    {
        $this->set(null);
    }

    /**
     * Check, if the value is cached at the moment.
     *
     * @return bool Is the cached value absent?
     */
    public function isEmpty()
    {
        return is_null($this->get());
    }

    /**
     * Get the config object.
     *
     * @return \oxConfig The config object.
     */
    private function getConfig()
    {
        return Registry::getConfig();
    }
}
