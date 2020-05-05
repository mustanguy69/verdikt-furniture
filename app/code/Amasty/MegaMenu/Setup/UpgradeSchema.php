<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Setup;

use Amasty\MegaMenu\Setup\Operation\AddLinkType;
use Amasty\MegaMenu\Setup\Operation\AddOrderTable;
use Amasty\MegaMenu\Setup\Operation\UpdateLink;
use Amasty\MegaMenu\Setup\Operation\UpdateStatus;
use Amasty\MegaMenu\Setup\Operation\UpdateStore;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var AddLinkType
     */
    private $addLinkType;

    /**
     * @var UpdateLink
     */
    private $updateLink;

    /**
     * @var UpdateStatus
     */
    private $updateStatus;

    /**
     * @var AddOrderTable
     */
    private $addOrderTable;

    /**
     * @var UpdateStore
     */
    private $updateStore;

    public function __construct(
        AddLinkType $addLinkType,
        UpdateLink $updateLink,
        UpdateStatus $updateStatus,
        AddOrderTable $addOrderTable,
        UpdateStore $updateStore
    ) {
        $this->addLinkType = $addLinkType;
        $this->updateLink = $updateLink;
        $this->updateStatus = $updateStatus;
        $this->addOrderTable = $addOrderTable;
        $this->updateStore = $updateStore;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->addLinkType->execute($setup);
            $this->updateLink->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->updateStatus->execute($setup);
            $this->addOrderTable->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->updateStore->execute($setup);
        }

        $setup->endSetup();
    }
}
