<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Test\Unit\Controller\Adminhtml\Link;

use Amasty\MegaMenu\Block\Html\Topmenu;
use Amasty\MegaMenu\Model\OptionSource\Status;
use Amasty\MegaMenu\Test\Unit\Traits;

/**
 * Class TopmenuTest
 *
 * @see Topmenu
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TopmenuTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var MockObject|Topmenu
     */
    private $topmenu;

    protected function setUp()
    {
        $this->topmenu = $this->getObjectManager()->getObject(Topmenu::class, []);
    }


    /**
     * @covers Topmenu::isNeedDisplay
     *
     * @dataProvider isNeedDisplayDataProvider
     *
     * @throws \ReflectionException
     */
    public function testIsNeedDisplay($id, $status, $view, $expectedResult)
    {
        $actualResult = $this->topmenu->isNeedDisplay($id, $status, $view);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Data provider for isNeedDisplay test
     * @return array
     */
    public function isNeedDisplayDataProvider()
    {
        return [
            [
                'category-node-1',
                Status::ENABLED,
                Topmenu::DESKTOP_VIEW,
                true
            ],
            [
                'custom-node-1',
                Status::ENABLED,
                Topmenu::DESKTOP_VIEW,
                true
            ],
            [
                'custom-node-1',
                Status::DESKTOP,
                Topmenu::DESKTOP_VIEW,
                true
            ],
            [
                'custom-node-1',
                Status::MOBILE,
                Topmenu::MOBILE_VIEW,
                true
            ],
            [
                'custom-node-1',
                Status::ENABLED,
                Topmenu::MOBILE_VIEW,
                true
            ],
            [
                'custom-node-1',
                Status::DESKTOP,
                Topmenu::MOBILE_VIEW,
                false
            ],
            [
                'custom-node-1',
                Status::MOBILE,
                Topmenu::DESKTOP_VIEW,
                false
            ]
        ];
    }
}
