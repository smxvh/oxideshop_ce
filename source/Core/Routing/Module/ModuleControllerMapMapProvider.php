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

use OxidEsales\EshopCommunity\Core\Contract\ControllerProviderInterface;

/**
 * Provide the controller mappings from the metadata of all active modules.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class ModuleControllerMapProvider implements ControllerProviderInterface
{
    private $cache = null;

    /**
     * Get the controller map of the modules.
     *
     * Returns an associative array, where
     *  - the keys are the controller ids
     *  - the values are the routed class names
     *
     * @return null|array The controller map of the modules.
     */
    public function getControllerMap()
    {
        $cache = $this->getCache();

        if ($cache->isEmpty()) {
            $result = $this->collectControllers();

            $cache->set($result);
        }

        return $this->combineAllModules();
    }

    /**
     * Add the given module controllers for module, given by its ID, to the cache.
     *
     * @param array $moduleId    The ID of the module controllers to add.
     * @param array $controllers The controllers to add to the cache.
     */
    public function addToCache($moduleId, $controllers)
    {
        $this->getCache()->add($moduleId, $controllers);
    }

    /**
     * Delete the controllers for the module, given by its ID, from the cache.
     *
     * @param string $moduleId The ID of the module, for which we want to delete the controllers from the cache.
     */
    public function removeFromCache($moduleId)
    {
        $this->getCache()->remove($moduleId);
    }

    /**
     * Collect the controller mappings from all activated modules.
     *
     * @return array All controller mappings of the activated modules, accessible by the moduleId.
     */
    private function collectControllers()
    {
        $result = [];

        // @todo: implement, walk over all active modules, and combine their controllers under their moduleId

        return $result;
    }

    /**
     * Combine the module controller arrays to one array.
     *
     * @return array An array with all module controller mappings.
     */
    private function combineAllModules()
    {
        $result = [];

        return $result;
    }

    /**
     * Getter for the controller module cache.
     *
     * @return ClassProviderStorage The controller module cache.
     */
    private function getCache()
    {
        $this->safeGuardCache();

        return $this->cache;
    }

    /**
     * Safe guard for the modules controllers cache.
     */
    private function safeGuardCache()
    {
        if (is_null($this->cache)) {
            $this->cache = oxNew(ClassProviderStorage::class);
        }
    }
}
