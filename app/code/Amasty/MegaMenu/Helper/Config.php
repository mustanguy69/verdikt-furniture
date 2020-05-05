<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Helper;

use Amasty\MegaMenu\Model\OptionSource\ColorTemplate;
use Amasty\MegaMenu\Model\OptionSource\MobileTemplate;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\DesignInterface;

class Config extends AbstractHelper
{
    const MODULE_PATH = 'ammegamenu/';

    const DEFAULT_BREAKPOINT = 1050;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    public function __construct(
        \Magento\Framework\Filter\FilterManager $filterManager,
        Context $context
    ) {
        parent::__construct($context);
        $this->filterManager = $filterManager;
    }

    /**
     * @param $path
     * @param int $storeId
     *
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::MODULE_PATH . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool)$this->getModuleConfig('general/enabled', $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isHamburgerEnabled($storeId = null)
    {
        return (bool)$this->getModuleConfig('general/hamburger_enabled', $storeId);
    }

    /**
     * @return string
     */
    public function getMobileTemplateClass()
    {
        return $this->getModuleConfig('general/mobile_template');
    }

    /**
     * @return string
     */
    public function getSubmenuBackgroundImage()
    {
        $mediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => 'media']) . 'amasty/megamenu/submenu_background_image/';
        $image = $this->getModuleConfig('color/submenu_background_image');
        $image = $image ? $mediaUrl . $image : '';

        return $image;
    }

    /**
     * @return string
     */
    public function getColorTemplate()
    {
        return (string) $this->getModuleConfig('color/color_template');
    }

    /**
     * @return bool
     */
    public function isSomeTemplateApplied()
    {
        return $this->getColorTemplate() !== ColorTemplate::BLANK;
    }
}
