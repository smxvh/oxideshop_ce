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
namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Routing\Module\ClassProviderStorage;
use OxidEsales\Eshop\Core\Exception\ModuleValidationException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use oxModuleCache;
use oxDb;
use OxidEsales\Eshop\Core\Module\ModuleExtensionsCleaner;
use OxidEsales\Eshop\Core\SettingsHandler;

/**
 * Modules installer class.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleInstaller extends \OxidEsales\Eshop\Core\Base
{
    /**
     * @var oxModuleCache
     */
    protected $_oModuleCache;

    /** @var ModuleExtensionsCleaner */
    private $moduleCleaner;

    /**
     * Sets dependencies.
     *
     * @param \OxidEsales\Eshop\Core\Module\ModuleCache $moduleCache
     * @param ModuleExtensionsCleaner                   $moduleCleaner
     */
    public function __construct(\OxidEsales\Eshop\Core\Module\ModuleCache $moduleCache = null, $moduleCleaner = null)
    {
        $this->setModuleCache($moduleCache);
        if (is_null($moduleCleaner)) {
            $moduleCleaner = oxNew(ModuleExtensionsCleaner::class);
        }
        $this->moduleCleaner = $moduleCleaner;
    }

    /**
     * Sets module cache.
     *
     * @param oxModuleCache $oModuleCache
     */
    public function setModuleCache($oModuleCache)
    {
        $this->_oModuleCache = $oModuleCache;
    }

    /**
     * Gets module cache.
     *
     * @return oxModuleCache
     */
    public function getModuleCache()
    {
        return $this->_oModuleCache;
    }

    /**
     * Activate extension by merging module class inheritance information with shop module array
     *
     * @param Module $module
     *
     * @return bool
     */
    public function activate(\OxidEsales\Eshop\Core\Module\Module $module)
    {
        $result = false;
        if ($moduleId = $module->getId()) {
            $this->_addExtensions($module);
            $this->_removeFromDisabledList($moduleId);

            if (version_compare($module->getMetaDataVersion(), '2.0', '<')) {
                /** Support for the key 'files' was removed in MetaData version 2.0 */
                $this->_addModuleFiles($module->getInfo("files"), $moduleId);
            }

            $this->_addTemplateBlocks($module->getInfo("blocks"), $moduleId);
            $this->_addTemplateFiles($module->getInfo("templates"), $moduleId);
            $settingsHandler = oxNew(SettingsHandler::class);
            $settingsHandler->setModuleType('module')->run($module);
            $this->_addModuleVersion($module->getInfo("version"), $moduleId);
            $this->_addModuleExtensions($module->getExtensions(), $moduleId);
            $this->_addModuleEvents($module->getInfo("events"), $moduleId);

            if (version_compare($module->getMetaDataVersion(), '2.0', '>=')) {
                try {
                    /** Support for the key 'controllers' was added in MetaData version 2.0 */
                    $this->addModuleControllers($module->getControllers(), $moduleId);
                } catch (ModuleValidationException $exception) {
                    $this->deactivate($module);
                    $lang = Registry::getLang();
                    $message = sprintf($lang->translateString('ERROR_METADATA_CONTROLLERS_NOT_UNIQUE', null, true), $exception->getMessage());

                    $standardException = oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class);
                    $standardException->setMessage($message);

                    throw $standardException;
                }
            }

            $this->resetCache();

            $this->_callEvent('onActivate', $moduleId);

            $result = true;
        }

        return $result;
    }

    /**
     * Deactivate extension by adding disable module class information to disabled module array
     *
     * @param Module $module
     *
     * @return bool
     */
    public function deactivate(\OxidEsales\Eshop\Core\Module\Module $module)
    {
        $result = false;
        if ($moduleId = $module->getId()) {
            $this->_callEvent('onDeactivate', $moduleId);

            $this->_addToDisabledList($moduleId);

            //removing recoverable options
            $this->_deleteBlock($moduleId);
            $this->_deleteTemplateFiles($moduleId);
            $this->_deleteModuleFiles($moduleId);
            $this->_deleteModuleEvents($moduleId);
            $this->_deleteModuleVersions($moduleId);
            $this->deleteModuleControllers($moduleId);

            $this->resetCache();

            $result = true;
        }

        return $result;
    }

    /**
     * Get parsed modules
     *
     * @return array
     */
    public function getModulesWithExtendedClass()
    {
        return $this->getConfig()->getModulesWithExtendedClass();
    }

    /**
     * Build module chains from nested array
     *
     * @param array $aModuleArray Module array (nested format)
     *
     * @return array
     */
    public function buildModuleChains($aModuleArray)
    {
        $aModules = array();
        if (is_array($aModuleArray)) {
            foreach ($aModuleArray as $sClass => $aModuleChain) {
                $aModules[$sClass] = implode('&', $aModuleChain);
            }
        }

        return $aModules;
    }

    /**
     * Diff two nested module arrays together so that the values of
     * $aRmModuleArray are removed from $aAllModuleArray
     *
     * @param array $aAllModuleArray All Module array (nested format)
     * @param array $aRemModuleArray Remove Module array (nested format)
     *
     * @return array
     */
    public function diffModuleArrays($aAllModuleArray, $aRemModuleArray)
    {
        if (is_array($aAllModuleArray) && is_array($aRemModuleArray)) {
            foreach ($aAllModuleArray as $sClass => $aModuleChain) {
                if (!is_array($aModuleChain)) {
                    $aModuleChain = array($aModuleChain);
                }
                if (isset($aRemModuleArray[$sClass])) {
                    if (!is_array($aRemModuleArray[$sClass])) {
                        $aRemModuleArray[$sClass] = array($aRemModuleArray[$sClass]);
                    }
                    $aAllModuleArray[$sClass] = array();
                    foreach ($aModuleChain as $sModule) {
                        if (!in_array($sModule, $aRemModuleArray[$sClass])) {
                            $aAllModuleArray[$sClass][] = $sModule;
                        }
                    }
                    if (!count($aAllModuleArray[$sClass])) {
                        unset($aAllModuleArray[$sClass]);
                    }
                } else {
                    $aAllModuleArray[$sClass] = $aModuleChain;
                }
            }
        }

        return $aAllModuleArray;
    }

    /**
     * Returns module cleaner object.
     *
     * @return ModuleExtensionsCleaner
     */
    protected function getModuleCleaner()
    {
        return $this->moduleCleaner;
    }

    /**
     * Add module to disable list
     *
     * @param string $sModuleId Module id
     */
    protected function _addToDisabledList($sModuleId)
    {
        $aDisabledModules = (array) $this->getConfig()->getConfigParam('aDisabledModules');

        $aModules = array_merge($aDisabledModules, array($sModuleId));
        $aModules = array_unique($aModules);

        $this->_saveToConfig('aDisabledModules', $aModules, 'arr');
    }

    /**
     * Removes extension from modules array.
     *
     * @deprecated on b-dev, This method is not used in code.
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteModule($sModuleId)
    {
        $aExt = $this->getConfig()->getModulesWithExtendedClass();

        $aUpdatedExt = $this->diffModuleArrays($aExt, $sModuleId);
        $aUpdatedExt = $this->buildModuleChains($aUpdatedExt);

        $this->getConfig()->saveShopConfVar('aarr', 'aModules', $aUpdatedExt);
    }

    /**
     * Deactivates or activates oxBlocks of a module
     *
     * @todo extract oxtplblocks query to ModuleTemplateBlockRepository
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteBlock($sModuleId)
    {
        $oDb = oxDb::getDb();
        $sShopId = $this->getConfig()->getShopId();
        $oDb->execute("DELETE FROM `oxtplblocks` WHERE `oxmodule` =" . $oDb->quote($sModuleId) . " AND `oxshopid` = " . $oDb->quote($sShopId));
    }

    /**
     * Add module template files to config for smarty.
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteTemplateFiles($sModuleId)
    {
        $aTemplates = (array) $this->getConfig()->getConfigParam('aModuleTemplates');
        unset($aTemplates[$sModuleId]);

        $this->_saveToConfig('aModuleTemplates', $aTemplates);
    }

    /**
     * Add module files
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteModuleFiles($sModuleId)
    {
        $aFiles = (array) $this->getConfig()->getConfigParam('aModuleFiles');
        unset($aFiles[$sModuleId]);

        $this->_saveToConfig('aModuleFiles', $aFiles);
    }

    /**
     * Removes module events
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteModuleEvents($sModuleId)
    {
        $aEvents = (array) $this->getConfig()->getConfigParam('aModuleEvents');
        unset($aEvents[$sModuleId]);

        $this->_saveToConfig('aModuleEvents', $aEvents);
    }

    /**
     * Removes module versions
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteModuleVersions($sModuleId)
    {
        $aVersions = (array) $this->getConfig()->getConfigParam('aModuleVersions');
        unset($aVersions[$sModuleId]);

        $this->_saveToConfig('aModuleVersions', $aVersions);
    }

    /**
     * Add extension to module
     *
     * @param OxidEsales\Eshop\Core\Module\Module $oModule
     */
    protected function _addExtensions(\OxidEsales\Eshop\Core\Module\Module $oModule)
    {
        $aModules = $this->_removeNotUsedExtensions($this->getModulesWithExtendedClass(), $oModule);

        if ($oModule->hasExtendClass()) {
            $aAddModules = $oModule->getExtensions();
            $aModules = $this->_mergeModuleArrays($aModules, $aAddModules);
        }

        $aModules = $this->buildModuleChains($aModules);

        $this->_saveToConfig('aModules', $aModules);
    }

    /**
     * Merge two nested module arrays together so that the values of
     * $aAddModuleArray are appended to the end of the $aAllModuleArray
     *
     * @param array $aAllModuleArray All Module array (nested format)
     * @param array $aAddModuleArray Added Module array (nested format)
     *
     * @return array
     */
    protected function _mergeModuleArrays($aAllModuleArray, $aAddModuleArray)
    {
        if (is_array($aAllModuleArray) && is_array($aAddModuleArray)) {
            foreach ($aAddModuleArray as $sClass => $aModuleChain) {
                if (!is_array($aModuleChain)) {
                    $aModuleChain = array($aModuleChain);
                }
                if (isset($aAllModuleArray[$sClass])) {
                    foreach ($aModuleChain as $sModule) {
                        if (!in_array($sModule, $aAllModuleArray[$sClass])) {
                            $aAllModuleArray[$sClass][] = $sModule;
                        }
                    }
                } else {
                    $aAllModuleArray[$sClass] = $aModuleChain;
                }
            }
        }

        return $aAllModuleArray;
    }

    /**
     * Removes module from disabled module list
     *
     * @param string $sModuleId Module id
     */
    protected function _removeFromDisabledList($sModuleId)
    {
        $aDisabledModules = (array) $this->getConfig()->getConfigParam('aDisabledModules');

        if (isset($aDisabledModules) && is_array($aDisabledModules)) {
            $aDisabledModules = array_diff($aDisabledModules, array($sModuleId));
            $this->_saveToConfig('aDisabledModules', $aDisabledModules, 'arr');
        }
    }

    /**
     * Add module templates to database.
     *
     * @todo extract oxtplblocks query to ]
     *
     * @param array  $moduleBlocks Module blocks array
     * @param string $moduleId     Module id
     */
    protected function _addTemplateBlocks($moduleBlocks, $moduleId)
    {
        $shopId = $this->getConfig()->getShopId();
        $db = oxDb::getDb();

        if (is_array($moduleBlocks)) {
            foreach ($moduleBlocks as $moduleBlock) {
                $id = Registry::getUtilsObject()->generateUId();

                $template = $moduleBlock["template"];
                $position = isset($moduleBlock['position']) && is_numeric($moduleBlock['position']) ?
                    intval($moduleBlock['position']) : 1;

                $block = $moduleBlock["block"];
                $filePath = $moduleBlock["file"];

                $theme = isset($moduleBlock['theme']) ? $moduleBlock['theme'] : '';

                $sql = "INSERT INTO `oxtplblocks` (`OXID`, `OXACTIVE`, `OXSHOPID`, `OXTHEME`, `OXTEMPLATE`, `OXBLOCKNAME`, `OXPOS`, `OXFILE`, `OXMODULE`)
                         VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?)";

                $db->execute($sql, array(
                    $id,
                    $shopId,
                    $theme,
                    $template,
                    $block,
                    $position,
                    $filePath,
                    $moduleId
                ));
            }
        }
    }

    /**
     * Add module files to config for auto loader.
     *
     * @param array  $aModuleFiles Module files array
     * @param string $sModuleId    Module id
     */
    protected function _addModuleFiles($aModuleFiles, $sModuleId)
    {
        $aFiles = (array) $this->getConfig()->getConfigParam('aModuleFiles');

        if (is_array($aModuleFiles)) {
            $aFiles[$sModuleId] = array_change_key_case($aModuleFiles, CASE_LOWER);
        }

        $this->_saveToConfig('aModuleFiles', $aFiles);
    }

    /**
     * Add module template files to config for smarty.
     *
     * @param array  $aModuleTemplates Module templates array
     * @param string $sModuleId        Module id
     */
    protected function _addTemplateFiles($aModuleTemplates, $sModuleId)
    {
        $aTemplates = (array) $this->getConfig()->getConfigParam('aModuleTemplates');
        if (is_array($aModuleTemplates)) {
            $aTemplates[$sModuleId] = $aModuleTemplates;
        }

        $this->_saveToConfig('aModuleTemplates', $aTemplates);
    }

    /**
     * Add module events to config.
     *
     * @param array  $aModuleEvents Module events
     * @param string $sModuleId     Module id
     */
    protected function _addModuleEvents($aModuleEvents, $sModuleId)
    {
        $aEvents = (array) $this->getConfig()->getConfigParam('aModuleEvents');
        if (is_array($aEvents)) {
            $aEvents[$sModuleId] = $aModuleEvents;
        }

        $this->_saveToConfig('aModuleEvents', $aEvents);
    }

    /**
     * Add module version to config.
     *
     * @param string $sModuleVersion Module version
     * @param string $sModuleId      Module id
     */
    protected function _addModuleVersion($sModuleVersion, $sModuleId)
    {
        $aVersions = (array) $this->getConfig()->getConfigParam('aModuleVersions');
        if (is_array($aVersions)) {
            $aVersions[$sModuleId] = $sModuleVersion;
        }

        $this->_saveToConfig('aModuleVersions', $aVersions);
    }

    /**
     * Add module id with extensions to config.
     *
     * @param array  $moduleExtensions Module version
     * @param string $moduleId         Module id
     */
    protected function _addModuleExtensions($moduleExtensions, $moduleId)
    {
        $extensions = (array) $this->getConfig()->getConfigParam('aModuleExtensions');
        if (is_array($extensions)) {
            $extensions[$moduleId] = array_values($moduleExtensions);
        }

        $this->_saveToConfig('aModuleExtensions', $extensions);
    }

    /**
     * Add controllers map for a given module Id to config
     *
     * @param array  $moduleControllers Map of controller ids and class names
     * @param string $moduleId          The Id of the module
     */
    protected function addModuleControllers($moduleControllers, $moduleId)
    {
        $this->validateModuleMetadataControllersOnActivation($moduleControllers);

        $classProviderStorage = $this->getClassProviderStorage();

        $classProviderStorage->add($moduleId, $moduleControllers);
    }

    /**
     * Remove controllers map for a given module Id from config
     *
     * @param string $moduleId The Id of the module
     */
    protected function deleteModuleControllers($moduleId)
    {
        $moduleControllerProvider = $this->getClassProviderStorage();

        $moduleControllerProvider->remove($moduleId);
    }

    /**
     * Call module event.
     *
     * @param string $sEvent    Event name
     * @param string $sModuleId Module Id
     */
    protected function _callEvent($sEvent, $sModuleId)
    {
        $aModuleEvents = (array) $this->getConfig()->getConfigParam('aModuleEvents');

        if (isset($aModuleEvents[$sModuleId], $aModuleEvents[$sModuleId][$sEvent])) {
            $mEvent = $aModuleEvents[$sModuleId][$sEvent];

            if (is_callable($mEvent)) {
                call_user_func($mEvent);
            }
        }
    }

    /**
     * Removes garbage ( module not used extensions ) from all installed extensions list
     *
     * @param array                                $installedExtensions Installed extensions
     * @param \OxidEsales\Eshop\Core\Module\Module $module              Module
     *
     * @deprecated on b-dev, ModuleExtensionsCleaner::cleanExtensions() should be used.
     *
     * @return array
     */
    protected function _removeNotUsedExtensions($installedExtensions, \OxidEsales\Eshop\Core\Module\Module $module)
    {
        return $this->getModuleCleaner()->cleanExtensions($installedExtensions, $module);
    }

    /**
     * Save module parameters to shop config
     *
     * @param string $sVariableName  config name
     * @param string $sVariableValue config value
     * @param string $sVariableType  config type
     */
    protected function _saveToConfig($sVariableName, $sVariableValue, $sVariableType = 'aarr')
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam($sVariableName, $sVariableValue);
        $oConfig->saveShopConfVar($sVariableType, $sVariableName, $sVariableValue);
    }

    /**
     * Resets modules cache.
     */
    protected function resetCache()
    {
        if ($this->getModuleCache()) {
            $this->getModuleCache()->resetCache();
        }
    }

    /**
     * @return \OxidEsales\Eshop\Core\Contract\ControllerMapProviderInterface
     */
    protected function getModuleControllerMapProvider()
    {
        return oxNew(\OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider::class);
    }

    /**
     * @return \OxidEsales\Eshop\Core\Contract\ControllerMapProviderInterface
     */
    protected function getShopControllerMapProvider()
    {
        return oxNew(\OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider::class);
    }

    /**
     * @return object
     */
    protected function getClassProviderStorage()
    {
        $classProviderStorage = oxNew(ClassProviderStorage::class);

        return $classProviderStorage;
    }

    /**
     * Ensure integrity of the controllerMap before storing it.
     * Both keys and values must be unique with in the same shop or sub-shop.
     *
     * @param array $moduleControllers
     *
     * @throws ModuleValidationException
     */
    protected function validateModuleMetadataControllersOnActivation($moduleControllers)
    {
        $moduleControllerMapProvider = $this->getModuleControllerMapProvider();
        $shopControllerMapProvider = $this->getShopControllerMapProvider();

        $moduleControllerMap = $moduleControllerMapProvider->getControllerMap();
        $shopControllerMap = $shopControllerMapProvider->getControllerMap();

        $existingMaps = array_merge($moduleControllerMap, $shopControllerMap);

        /**
         * Ensure, that controller keys are unique.
         * As keys are always stored in lower case, we must test against lower case keys here as well
         */
        $duplicatedKeys = array_intersect_key(array_change_key_case($moduleControllers, CASE_LOWER), $existingMaps);
        if (!empty($duplicatedKeys)) {
            throw new \OxidEsales\Eshop\Core\Exception\ModuleValidationException(implode(',', $duplicatedKeys));
        }

        /**
         * Ensure, that controller values are unique.
         */
        $duplicatedValues = array_intersect($moduleControllers, $existingMaps);
        if (!empty($duplicatedValues)) {
            throw new \OxidEsales\Eshop\Core\Exception\ModuleValidationException(implode(',', $duplicatedValues));
        }
    }
}
