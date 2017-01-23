<?php
namespace Integration\Modules;

use Integration\Modules\TestData\modules\Vendor1\ModuleInheritance17\MyClass as MyClass1_7;
use OxidEsales\EshopCommunity\Core\Registry;

require_once __DIR__ . '/BaseModuleTestCase.php';


/**
 * THIS IS A DRAFT
 */

class ModuleInheritanceTest extends BaseModuleTestCase {


    /**
     * @var Environment The helper object for the environment.
     */
    protected $environment = null;

    /**
     * Standard set up method. Calls parent first.
     */
    public function setUp()
    {
        parent::setUp();

        $configFile = Registry::get('oxConfigFile');
        $configFile->setVar('sShopDir', realpath(__DIR__ . '/TestData'));

        Registry::set('oxConfigFile', $configFile);

        $this->environment = new Environment();
    }

    /**
     * Standard tear down method. Calls parent last.
     */
    public function tearDown()
    {
        $this->environment->clean();

        parent::tearDown();
    }

    /**
     * This test covers PHP inheritance between one module class and one shop class.
     *
     * The module class extends the PHP class directly like '<module class> extends <shop class>'
     * In this case the parent class of the module class must be the shop class as instantiated with oxNew
     *
     * @dataProvider dataProviderTestModuleInheritanceTestPhpInheritance
     */
    public function testModuleInheritanceTestPhpInheritance($moduleToActivate, $moduleClassName, $shopClassName )
    {
        $this->environment->prepare([$moduleToActivate]);

        $modelClass = oxNew($moduleClassName);

        $shopClass = oxNew($shopClassName);
        $modelClassParent = get_parent_class($modelClass);

        $this->assertEquals($modelClassParent, $shopClass);
    }

    public function dataProviderTestModuleInheritanceTestPhpInheritance() {
        return [
            [
            'moduleToActivate' => 'module_inheritance_1_7',
            'moduleClassName' => MyClass1_7::class,
            'shopClassName' => \OxidEsales\EshopCommunity\Application\Model\Article::class
            ],
            /**
             * ETC.
             */
        ];
    }
}
