<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Controller\Adminhtml\Builder;

use Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position;
use Magento\Backend\App\Action;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_MegaMenu::menu_builder';

    /**
     * @var Position
     */
    private $positionResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Action\Context $context,
        Position $positionResource,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->positionResource = $positionResource;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store', $this->storeManager->getDefaultStoreView()->getId());
        $this->positionResource->importCategoryPositions($storeId);

        $this->initAction();

        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Menu Builder'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Menu Builder'));

        $this->_view->renderLayout();
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    private function initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(self::ADMIN_RESOURCE)
            ->_addBreadcrumb(__('Menu Builder'), __('Menu Builder'));

        return $this;
    }
}
