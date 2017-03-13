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
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use Exception;
use oxDb;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;
use oxRegistry;

class Environment
{
    /**
     * Shop Id in which will be prepared environment.
     *
     * @var int
     */
    protected $shopId;

    /**
     * Path to be used as sShopPath for testing
     *
     * @var string
     */
    protected $path = null;

    /**
     * Name of fixture directory containing the test modules
     *
     * @var string
     */
    protected $fixtureDirectory = 'TestData';

    /**
     *
     */
    public function __construct($path = __DIR__)
    {
        $this->path = $path;
    }

    /**
     * Sets shop Id for modules environment.
     *
     * @param int $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
        Registry::getConfig()->setShopId($shopId);
        $utilsObject = new \OxidEsales\Eshop\Core\UtilsObject();
        Registry::set('oxUtilsObject', $utilsObject);
        $this->loadShopParameters();
    }

    /**
     * Returns shop Id.
     *
     * @return int
     */
    public function getShopId()
    {
        return is_null($this->shopId) ? 1 : $this->shopId;
    }

    /**
     * Loads and activates modules by given IDs.
     *
     * @param null $modules
     *
     * @throws Exception
     */
    public function prepare($modules = null)
    {
        $this->clean();
        $this->setShopConfigParameters();

        if (is_null($modules)) {
            $modules = $this->getAllModules();
        }

        $this->activateModules($modules);
    }

    /**
     * Cleans modules environment.
     */
    public function clean()
    {
        $config = Registry::getConfig();
        $config->setConfigParam('aModules', null);
        $config->setConfigParam('aModuleTemplates', null);
        $config->setConfigParam('aDisabledModules', array());
        $config->setConfigParam('aModuleFiles', null);
        $config->setConfigParam('aModuleVersions', null);
        $config->setConfigParam('aModuleEvents', null);
        $config->setConfigParam('aModuleControllers', null);

        $database = oxDb::getDb();
        $database->execute("DELETE FROM `oxconfig` WHERE `oxmodule` LIKE 'module:%' OR `oxvarname` LIKE '%Module%'");
        $database->execute('TRUNCATE `oxconfigdisplay`');
        $database->execute('TRUNCATE `oxtplblocks`');
    }

    /**
     * Set the shop config parameters shopId and sShopDir
     */
    public function setShopConfigParameters()
    {
        $oConfig = Registry::getConfig();
        $oConfig->setShopId($this->getShopId());
        $oConfig->setConfigParam('sShopDir', $this->getPathToTestDataDirectory());
    }

    /**
     * Activates given modules.
     *
     * @param array $modules
     *
     * @throws Exception If the module could not be loaded or activated.
     */
    public function activateModules($modules)
    {
        foreach ($modules as $moduleId) {
            $this->activateModuleById($moduleId);
        }
    }

    /**
     * Activate the module with the given ID.
     *
     * @param string $moduleId The ID of the module to activate.
     *
     * @throws Exception If the module could not be loaded or activated.
     */
    public function activateModuleById($moduleId)
    {
        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);

        if ($module->load($moduleId)) {
            $moduleInstaller = $this->getModuleInstaller($module);

            if (!$moduleInstaller->activate($module)) {
                throw new Exception("Module $moduleId was not activated.");
            }
        } else {
            throw new Exception("Module $moduleId was not activated.");
        }
    }

    /**
     * Deactivate a module, given by its ID.
     *
     * @param string $moduleId The ID of the module we want to deactivate.
     *
     * @throws Exception
     */
    public function deactivateModuleById($moduleId)
    {
        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);

        if ($module->load($moduleId)) {
            $moduleInstaller = $this->getModuleInstaller($module);

            if (!$moduleInstaller->deactivate($module)) {
                throw new Exception("Module $moduleId was not deactivated.");
            }
        } else {
            throw new Exception("Module $moduleId was not deactivated. Couldn't find/load the module.");
        }
    }

    /**
     * Returns fixtures directory.
     *
     * @return string
     */
    protected function getPathToTestDataDirectory()
    {
        return realpath($this->path) . '/' . $this->fixtureDirectory  . '/';
    }

    /**
     * Scans directory and returns modules IDs.
     *
     * @return array
     */
    protected function getAllModules()
    {
        $aModules = array_diff(scandir($this->getPathToTestDataDirectory() . 'modules'), array('..', '.'));

        return $aModules;
    }

    /**
     * Loads config parameters from DB and sets to config.
     */
    protected function loadShopParameters()
    {
        $aParameters = array(
            'aModules', 'aModuleEvents', 'aModuleVersions', 'aModuleFiles', 'aDisabledModules', 'aModuleTemplates', 'aModuleControllers'
        );
        foreach ($aParameters as $sParameter) {
            Registry::getConfig()->setConfigParam($sParameter, $this->_getConfigValueFromDB($sParameter));
        }
    }

    /**
     * Returns config values from table oxconfig by field- oxvarname.
     *
     * @param string $sVarName
     *
     * @return array
     */
    protected function _getConfigValueFromDB($sVarName)
    {
        $oDb = oxDb::getDb();
        $sQuery = "SELECT " . Registry::getConfig()->getDecodeValueQuery() . "
                   FROM `oxconfig`
                   WHERE `OXVARNAME` = '{$sVarName}'
                   AND `OXSHOPID` = {$this->getShopId()}";

        $sResult = $oDb->getOne($sQuery);
        $aExtensionsToCheck = $sResult ? unserialize($sResult) : array();

        return $aExtensionsToCheck;
    }

    /**
     * Get the module installer with a given module set in the module cache.
     *
     * @param Module $module
     *
     * @return object
     */
    protected function getModuleInstaller($module)
    {
        $moduleCache = oxNew(\OxidEsales\Eshop\Core\Module\ModuleCache::class, $module);
        $moduleInstaller = oxNew(\OxidEsales\Eshop\Core\Module\ModuleInstaller::class, $moduleCache);

        return $moduleInstaller;
    }
}
