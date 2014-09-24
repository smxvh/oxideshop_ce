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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Returns shop path.
 *
 * @return string
 */
function getShopBasePath()
{
    return oxPATH;
}

require_once 'unit/test_utils.php';
require_once getShopBasePath() . 'core/oxfunctions.php';

// Get db instance.
$oConfigFile = new oxConfigFile(getShopBasePath() . "config.inc.php");

oxRegistry::set("OxConfigFile", $oConfigFile);
oxRegistry::set("oxConfig", new oxConfig());

$oDb = new oxDb();
$oDb->setConfig($oConfigFile);
$oLegacyDb = $oDb->getDb();
OxRegistry::set('OxDb', $oLegacyDb);

oxRegistry::getConfig();
require_once getShopBasePath() . 'core/oxutils.php';
require_once getShopBasePath() . 'core/adodblite/adodb.inc.php';
require_once getShopBasePath() . 'core/oxsession.php';
require_once getShopBasePath() . 'core/oxconfig.php';

require_once 'acceptance/library/oxTestCase.php';
require_once TEST_LIBRARY_PATH.'vendor/autoload.php';

define('hostUrl', getenv('SELENIUM_SERVER')? getenv('SELENIUM_SERVER') : $sSeleniumServerIp );
define('browserName', getenv('BROWSER_NAME')? getenv('BROWSER_NAME') : $sBrowserName );

$sShopUrl = getenv('SELENIUM_TARGET')? getenv('SELENIUM_TARGET') : $sShopUrl;

define ( 'SELENIUM_SCREENSHOTS_PATH', getenv('SELENIUM_SCREENSHOTS_PATH')? getenv('SELENIUM_SCREENSHOTS_PATH') : $sSeleniumScreenShotsPath );
define ( 'SELENIUM_SCREENSHOTS_URL', getenv('SELENIUM_SCREENSHOTS_URL')? getenv('SELENIUM_SCREENSHOTS_URL') : $sSeleniumScreenShotsUrl );
define ('DEMO_DATA_FILE', getenv('DEMO_DATA_FILE')? getenv('DEMO_DATA_FILE') : $sDemoDataFileName);

if (SELENIUM_SCREENSHOTS_PATH && !is_dir(SELENIUM_SCREENSHOTS_PATH)) {
    mkdir(SELENIUM_SCREENSHOTS_PATH, 0777, 1);
}

if (getenv('OXID_LOCALE') == 'international') {
    define('oxTESTSUITEDIR', 'acceptanceInternational');
} elseif (getenv('OXID_TEST_EFIRE') ? getenv('OXID_TEST_EFIRE') : $sModule) {
    define('oxTESTSUITEDIR', 'acceptanceEfire');
} else {
    define('oxTESTSUITEDIR', 'acceptance');
}

if (INSTALLSHOP) {
    $oFileCopier = new oxFileCopier();
    $oFileCopier->copyFiles(TESTS_DIRECTORY . 'acceptance/testData/', oxPATH);
}

if (INSTALLSHOP) {
    $oCurl = new oxTestCurl();
    $oCurl->setUrl(shopURL . '/Services/_db.php');
    $oCurl->setParameters(array(
            'serial' => TEST_SHOP_SERIAL,
            'addDemoData' => 1,
            'turnOnVarnish' => OXID_VARNISH,
        ));
    $sResponse = $oCurl->execute();
}

$oServiceCaller = new oxServiceCaller();

if (!SKIPSHOPSETUP && !$sModule) {
    $sFileName = "acceptance/demodata_PE.sql";
    $oServiceCaller->setParameter('importSql', '@'.$sFileName);
    $oServiceCaller->callService('ShopPreparation', 1);

}

if ($sModule) {
    $sFileName = oxTESTSUITEDIR . '/' . $sModule . "/demodata_PE.sql";
    if (file_exists($sFileName)) {
        $oServiceCaller->setParameter('importSql', '@'.$sFileName);
        $oServiceCaller->callService('ShopPreparation', 1);
    }
    putenv("TEST_FILE_FILTER=$sModule");
}

// dumping database for selenium tests
$oServiceCaller->setParameter('dumpDB', true);
$oServiceCaller->setParameter('dump-prefix', 'reset_suite_db_dump');
$oServiceCaller->callService('ShopPreparation', 1);
