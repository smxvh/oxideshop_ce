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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Core\Autoload;

/**
 * This is an autoloader that performs several tricks to provide class aliases
 * for the new namespaced classes.
 *
 * The aliases are provided by a class map provider. But it is not sufficient
 * to
 */
class AliasAutoload
{

    private $classMapProvider;
    private $backwardsCompatibilityClassMap;
    private $reverseBackwardsCompatibilityClassMap; // real class name => lowercase(old class name)


    /**
     * BcAliasAutoloader constructor.
     */
    public function __construct()
    {
        $classMap = include_once __DIR__ . DIRECTORY_SEPARATOR . 'BackwardsCompatibilityClassMap.php';
        $this->backwardsCompatibilityClassMap = array_map('strtolower', $classMap);
    }

    /**
     * @param string $class
     *
     * @return null
     */
    public function autoload($class)
    {
        $bcAlias = null;
        $virtualAlias = null;
        $realClass = null;

        if ($this->isBcAliasRequest($class)) {
            $bcAlias = $class;
            $virtualAlias = $this->getVirtualAliasForBcAlias($class);
        }

        if ($this->isVirtualClassRequest($class)) {
            $virtualAlias = $class;
            $bcAlias = $this->getBcAliasForVirtualAlias($class);
        }

        if ($virtualAlias) {
            $realClass = $this->getRealClassForVirtualAlias($virtualAlias);
        }

        if (!$realClass) {
            return false;
        }

        $this->forceClassLoading($realClass);

        $declaredClasses = get_declared_classes();
        if ($bcAlias && !in_array(strtolower($bcAlias), $declaredClasses)) {
            class_alias($realClass, $bcAlias);
        }
        if ($virtualAlias && !in_array(strtolower($virtualAlias), $declaredClasses)) {
            class_alias($realClass, $virtualAlias);

            return true; // Implies also generating of $bcAlias
        }

        return false;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    private function isBcAliasRequest($class)
    {
        $classMap = $this->getBackwardsCompatibilityClassMap();

        return in_array(strtolower($class), $classMap);
    }

    /**
     * @param string $class
     *
     * @return mixed
     */
    private function getVirtualAliasForBcAlias($class)
    {
        $classMap = array_flip($this->getBackwardsCompatibilityClassMap());

        return $classMap[strtolower($class)];
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    private function isVirtualClassRequest($class)
    {
        return strpos($class, 'OxidEsales\\Eshop\\') === 0;
    }

    /**
     * @param string $class
     *
     * @return mixed
     */
    private function getBcAliasForVirtualAlias($class)
    {
        $classMap = $this->getBackwardsCompatibilityClassMap();
        if (key_exists($class, $classMap)) {
            return $classMap[$class];
        }
    }

    /**
     * @param string $class
     *
     * @return string
     */
    private function getRealClassForVirtualAlias($class)
    {
        $virtualClassMap = $this->getVirtualClassMap();

        if (key_exists($class, $virtualClassMap)) {
            return $virtualClassMap[$class];
        } else {
            return null;
        }
    }

    /**
     * @param string $class
     */
    private function forceClassLoading($class)
    {
        // Calling class_exists will trigger the autoloader
        class_exists($class);
    }

    /**
     * @return array
     */
    private function getBackwardsCompatibilityClassMap()
    {
        return $this->backwardsCompatibilityClassMap;
    }

    /**
     * @return array
     */
    private function getReverseClassMap()
    {
        if (!$this->reverseBackwardsCompatibilityClassMap) {
            $this->reverseBackwardsCompatibilityClassMap = array_flip($this->getBackwardsCompatibilityClassMap());
        }

        return $this->reverseBackwardsCompatibilityClassMap;
    }

    /**
     * @return array
     */
    private function getVirtualClassMap()
    {
        /** The properties defined in the config file will dynamically loaded into this class */
        include OX_BASE_PATH . DIRECTORY_SEPARATOR . 'config.inc.php';
        $edition = $this->edition;
        $virtualClassMap = [];

        if ($edition == 'EE' &&
            file_exists(VENDOR_PATH . 'oxid-esales/oxideshop-ee/Core/Autoload/VirtualNameSpaceClassMap.php')
        ) {
            include_once VENDOR_PATH . 'oxid-esales/oxideshop-ee/Core/Autoload/VirtualNameSpaceClassMap.php';
            $virtualNameSpaceClassMap = new \OxidEsales\EshopEnterprise\Core\Autoload\VirtualNameSpaceClassMap();
            $virtualClassMap = $virtualNameSpaceClassMap->getClassMap();
        } elseif ($edition == 'PE' &&
            file_exists(VENDOR_PATH . 'oxid-esales/oxideshop-pe/Core/Autoload/VirtualNameSpaceClassMap.php')
        ) {
            include_once VENDOR_PATH . 'oxid-esales/oxideshop-pe/Core/Autoload/VirtualNameSpaceClassMap.php';
            $virtualNameSpaceClassMap = new \OxidEsales\EshopProfessional\Core\Autoload\VirtualNameSpaceClassMap();
            $virtualClassMap = $virtualNameSpaceClassMap->getClassMap();
        } elseif ($edition == 'CE' &&
            file_exists(OX_BASE_PATH . 'Core/Autoload/VirtualNameSpaceClassMap.php')
        ) {
            include_once OX_BASE_PATH . 'Core/Autoload/VirtualNameSpaceClassMap.php';
            $virtualNameSpaceClassMap = new \OxidEsales\EshopCommunity\Core\Autoload\VirtualNameSpaceClassMap();
            $virtualClassMap = $virtualNameSpaceClassMap->getClassMap();
        } else {
            trigger_error('The corresponding classmap for edition "' . $edition . '" was not found', E_USER_ERROR);
        }

        return $virtualClassMap;
    }
}
spl_autoload_register([new AliasAutoload(), 'autoload']);
