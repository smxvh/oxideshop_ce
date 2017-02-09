#!/usr/bin/php
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

// namespace OxidEsales\EshopCommunity;

/**
 * Use output buffer to avoid warnings about sending cookies
 */
ob_start();

if (version_compare(PHP_VERSION, '7.0', '<')) {
    /**
     * @param string $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     *
     * @return bool
     */
    function myErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if (E_RECOVERABLE_ERROR === $errno) {
            echo "FAIL next type hint: " . $errstr . PHP_EOL;

            return true;
        }

        return false;
    }

    set_error_handler('myErrorHandler');
}


require_once 'bootstrap.php';

error_reporting(E_ALL);

/**
 * Test instance of oxException (camelCase) created with oxNew
 */
function testCaseOxNewInstanceOfoxExceptionCamelCase()
{
    echo '-----------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . PHP_EOL;
    echo '-----------------------------------------------' . PHP_EOL;
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', '\OxidEsales\Eshop\Core\Exception\StandardException');

    $className = 'oxException';

    testReportExceptionClassName($className);
}

/**
 * Test instance of oxException (lowercase) created with oxNew
 */
function testCaseOxNewInstanceOfoxExceptionLowercase()
{
    echo '-----------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . PHP_EOL;
    echo '-----------------------------------------------' . PHP_EOL;
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', '\OxidEsales\Eshop\Core\Exception\StandardException');

    $className = 'oxexception';

    testReportExceptionClassName($className);
}

/**
 * Test instance of \OxidEsales\EshopCommunity\Core\Exception\StandardException created with oxNew
 */
function testCaseOxNewInstanceOfStandardException()
{
    echo '-----------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . PHP_EOL;
    echo '-----------------------------------------------' . PHP_EOL;
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', '\OxidEsales\Eshop\Core\Exception\StandardException');
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', 'oxexception');

    echo '$exception = oxNew(\OxidEsales\EshopCommunity\Core\Exception\StandardException::class);' . PHP_EOL;

    $className = \OxidEsales\EshopCommunity\Core\Exception\StandardException::class;

    testReportExceptionClassName($className);
}

/**
 * Test instance of \OxidEsales\Eshop\Core\Exception\StandardException created with oxNew
 */
function testCaseOxNewInstanceOfVirtualStandardException()
{
    echo '-----------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . PHP_EOL;
    echo '-----------------------------------------------' . PHP_EOL;
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', '\OxidEsales\Eshop\Core\Exception\StandardException');
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', 'oxexception');

    $className = \OxidEsales\Eshop\Core\Exception\StandardException::class;

    testReportExceptionClassName($className);
}

/**
 * Test instance of oxException (camelCase) created with oxNew
 */
function testCaseNewInstanceOfoxExceptionCamelCase()
{
    echo '-----------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . PHP_EOL;
    echo '-----------------------------------------------' . PHP_EOL;
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', '\OxidEsales\Eshop\Core\Exception\StandardException');

    $className = 'oxException';

    testReportExceptionClassName($className, false);
}

/**
 * Test instance of oxException (lowercase) created with oxNew
 */
function testCaseNewInstanceOfoxExceptionLowercase()
{
    echo '-----------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . PHP_EOL;
    echo '-----------------------------------------------' . PHP_EOL;
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', '\OxidEsales\Eshop\Core\Exception\StandardException');

    $className = 'oxexception';

    testReportExceptionClassName($className, false);
}

/**
 * Test instance of \OxidEsales\EshopCommunity\Core\Exception\StandardException created with oxNew
 */
function testCaseNewInstanceOfStandardException()
{
    echo '-----------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . PHP_EOL;
    echo '-----------------------------------------------' . PHP_EOL;
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', '\OxidEsales\Eshop\Core\Exception\StandardException');
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', 'oxexception');

    echo '$exception = oxNew(\OxidEsales\EshopCommunity\Core\Exception\StandardException::class);' . PHP_EOL;

    $className = \OxidEsales\EshopCommunity\Core\Exception\StandardException::class;

    testReportExceptionClassName($className, false);
}

/**
 * Test instance of \OxidEsales\Eshop\Core\Exception\StandardException created with oxNew
 */
function testCaseNewInstanceOfVirtualStandardException()
{
    echo '-----------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . PHP_EOL;
    echo '-----------------------------------------------' . PHP_EOL;
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', '\OxidEsales\Eshop\Core\Exception\StandardException');
    class_alias('\OxidEsales\EshopCommunity\Core\Exception\StandardException', 'oxexception');

    $className = \OxidEsales\Eshop\Core\Exception\StandardException::class;

    testReportExceptionClassName($className, false);
}

/**
 * Test behaviour of a certain exception class name
 *
 * @param string $className
 * @param bool   $oxnew
 */
function testReportExceptionClassName($className, $oxnew = true)
{

    echo 'Creating an instance like this:' . PHP_EOL;
    if ($oxnew) {
        echo '$exception = oxNew(' . $className . ');' . PHP_EOL;
        echo PHP_EOL;
        echo 'Following autoloaders are called:' . PHP_EOL;
        echo '-----------------------------------------------' . PHP_EOL;
        /** @var \Exception $exception */
        $exception = oxNew($className);
    } else {
        echo '$exception = new ' . $className . '();' . PHP_EOL;
        echo PHP_EOL;
        echo 'Following autoloaders are called:' . PHP_EOL;
        echo '-----------------------------------------------' . PHP_EOL;
        /** @var \Exception $exception */
        $exception = new $className();
    }
    echo '-----------------------------------------------' . PHP_EOL;


    echo PHP_EOL;
    echo '$exception instanceof \Exception' . PHP_EOL;;
    var_dump($exception instanceof \Exception);

    echo PHP_EOL;
    echo '$exception instanceof \oxException' . PHP_EOL;;
    var_dump($exception instanceof \oxException);

    echo PHP_EOL;
    echo '$exception instanceof \OxidEsales\EshopCommunity\Core\Exception\StandardException' . PHP_EOL;;
    var_dump($exception instanceof \OxidEsales\EshopCommunity\Core\Exception\StandardException);

    echo PHP_EOL;
    echo '$exception instanceof \OxidEsales\Eshop\Core\Exception\StandardException' . PHP_EOL;;
    var_dump($exception instanceof \OxidEsales\Eshop\Core\Exception\StandardException);

    echo PHP_EOL;

    try {
        try {
            throw $exception;
        } catch (\Exception $exception) {
            echo 'Caught \Exception' . PHP_EOL;
        }

        try {
            throw $exception;
        } catch (\oxException $exception) {
            echo 'Caught \oxException' . PHP_EOL;
        }

        try {
            throw $exception;
        } catch (oxException $exception) {
            echo 'Caught oxException' . PHP_EOL;
        }

        try {
            throw $exception;
        } catch (\OxidEsales\EshopCommunity\Core\Exception\StandardException $exception) {
            echo 'Caught \OxidEsales\EshopCommunity\Core\Exception\StandardException' . PHP_EOL;
        }

        try {
            throw $exception;
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
            echo 'Caught \OxidEsales\Eshop\Core\Exception\StandardException' . PHP_EOL;
        }
    } catch (\Exception $exception) {
        echo 'Uncaught Exception' . PHP_EOL;
    }
    echo '-----------------------------------------------' . PHP_EOL;
    echo PHP_EOL;
    echo PHP_EOL;
    echo PHP_EOL;
}

/**
 * Test instance of oxArticle (camelCase) created with oxNew
 *
 * @param null $realClass
 * @param null $virtualClass
 * @param null $alias
 * @param bool $oxnew
 * @param bool $class_alias_autoload
 */
function testCaseNewClass($realClass = null, $virtualClass = null, $alias = null, $oxnew = true, $class_alias_autoload = true)
{
    $realClass = '\OxidEsales\EshopCommunity\Application\Model\Article';
    $virtualClass = '\OxidEsales\Eshop\Application\Model\Article';
    $alias = 'oxArticle';
    $oxnew = true;
    $class_alias_autoload = false;

    echo '-----------------------------------------------' . PHP_EOL;
    echo __FUNCTION__ . PHP_EOL;
    echo '-----------------------------------------------' . PHP_EOL;
    class_alias($realClass, $virtualClass, $class_alias_autoload);
    class_alias($realClass, $alias, $class_alias_autoload);

    $className = $alias;

    echo 'Creating an instance like this:' . PHP_EOL;
    if ($oxnew) {
        echo '$object = oxNew(' . $className . ');' . PHP_EOL;
        echo PHP_EOL;
        echo 'Following autoloaders are called:' . PHP_EOL;
        echo '-----------------------------------------------' . PHP_EOL;
        $object = oxNew($className);
    } else {
        echo '$object = new ' . $className . '();' . PHP_EOL;
        echo PHP_EOL;
        echo 'Following autoloaders are called:' . PHP_EOL;
        echo '-----------------------------------------------' . PHP_EOL;
        /** @var \Exception $exception */
        $object = new $className();
    }
    echo '-----------------------------------------------' . PHP_EOL;

    echo PHP_EOL;
    echo 'parent classes of $object ' . get_class($object). PHP_EOL;
    echo '-----------------------------------------------' . PHP_EOL;
    var_dump(class_parents($object));
    echo '-----------------------------------------------' . PHP_EOL;

    $class = $className;
    testInstanceOf($object, $class);

    $class = '\\' . $className;
    testInstanceOf($object, $class);

    $class = \OxidEsales\EshopCommunity\Application\Model\Article::class;
    testInstanceOf($object, $class);

    $class = \OxidEsales\Eshop\Application\Model\Article::class;
    testInstanceOf($object, $class);

    try {
        /**
         * @param oxarticle $object
         */
        $functionWithTypeHintBcLowerCase = function (oxarticle $object) {
            echo 'PASS Type hint (lowerCase) for BC' . PHP_EOL;
        };
        $functionWithTypeHintBcLowerCase($object);
    } catch (Error $error) {
        echo PHP_EOL;
        echo 'FAIL Type hint (lowerCase) for BC' . PHP_EOL;
        echo $error->getMessage() . PHP_EOL;
    }

    try {
        /**
         * @param oxArticle $object
         */
        $functionWithTypeHintBcCamelCase = function (oxArticle $object) {
            echo 'PASS Type hint (camelCase) for BC' . PHP_EOL;
        };
        $functionWithTypeHintBcCamelCase($object);
    } catch (Error $error) {
        echo PHP_EOL;
        echo 'FAIL Type hint (camelCase) for BC' . PHP_EOL;
        echo $error->getMessage() . PHP_EOL;
    }

    /**
     * @param oxArticle $object
     */
    $functionWithTypeHintCommunityNs = function (\OxidEsales\EshopCommunity\Application\Model\Article $object) {
        echo 'PASS Type hint for Community Namespace' . PHP_EOL;
    };

    try {
        $functionWithTypeHintCommunityNs($object);
    } catch (Error $error) {
        echo PHP_EOL;
        echo 'FAIL Type hint for Community Namespace' . PHP_EOL;
        echo $error->getMessage() . PHP_EOL;
    }

    /**
     * @param oxArticle $object
     */
    $functionWithTypeHintVirtual = function (\OxidEsales\Eshop\Application\Model\Article $object) {
        echo 'PASS Type hint for Virtual Namespace' . PHP_EOL;
    };

    try {
        $functionWithTypeHintVirtual($object);
    } catch (Error $error) {
        echo PHP_EOL;
        echo 'FAIL Type hint for Virtual Namespace' . PHP_EOL;
        echo $error->getMessage() . PHP_EOL;
    }
}

/**
 * Test if a given object is instance of a given class
 *
 * @param object $object
 * @param string $class
 */
function testInstanceOf($object, $class)
{
    $result = $object instanceof $class ? 'PASS' : 'FAIL';
    echo $result . ' $object instanceof ' . $class . PHP_EOL;
}

$func = $argv[1];
$func();

$out = ob_get_clean();

echo $out;
